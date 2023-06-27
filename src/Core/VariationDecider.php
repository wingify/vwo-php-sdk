<?php

/**
 * Copyright 2019-2022 Wingify Software Pvt. Ltd.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace vwo\Core;

use Exception as Exception;
use Monolog\Logger as Logger;
use vwo\Constants\CampaignTypes;
use vwo\Constants\Hooks;
use vwo\Utils\Common as CommonUtil;
use vwo\Utils\Campaign as CampaignUtil;
use vwo\Services\LoggerService;
use vwo\Services\HooksManager;
use vwo\Core\Bucketer as Bucketer;
use vwo\Utils\ImpressionBuilder;
use vwo\Utils\UuidUtil;
use vwo\Utils\Validations as ValidationsUtil;

class VariationDecider
{
    public $hasStoredVariation;
    private $accountId;
    private $hooksManager;
    private $settings;

    const CLASSNAME = 'vwo\Core\VariationDecider';
    const RandomAlgo = 1;

    function __construct($settings = null)
    {
        $this->settings = $settings;
    }

    public function getAccountId()
    {
        return $this->accountId;
    }

    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    public function setHooksManager($hooksManager)
    {
        $this->hooksManager = $hooksManager;
    }

    public function getHooksManager()
    {
        return $this->hooksManager;
    }

    /**
     * Returns variation for the user for given campaign
     * This method achieves the variation assignment in the following way:
     * If campaign is part of any group, the winner is found in the following way:
     * 1. Check whitelisting for called campaign, if passed return targeted variation.
     * 2. Check user storage for called campaign, if passed return stored variation.
     * 3. Check presegmentation and traffic allocation for called campaign, if passed then
     * check whitelisting and user storage for other campaigns of same group if any
     * campaign passes return None else find eligible campaigns
     * 4. Find winner campaign from eligible campaigns and if winner campaign is same as
     * called campaign return bucketed variation and store variation in user storage,
     * however if winner campaign is not called campaign return None
     *
     * However if campaign is not part of any group, then this method achieves the variation
     * assignment in the following way:
     * 1. First get variation from UserStorage, if variation is found in user_storage_data,
     * return from there
     * 2. Evaluates white listing users for each variation, and find a targeted variation.
     * 3. If no targeted variation is found, evaluate pre-segmentation result
     * 4. Evaluate percent traffic
     * 5. If user becomes part of campaign assign a variation.
     * 6. Store the variation found in the user_storage
     *
     * @param  $userStorageObj
     * @param  $campaign
     * @param  $userId
     * @param  array $options
     * @param  $apiName
     * @param  $goalIdentifier
     * @return array|mixed|null
     */
    public function fetchVariationData($userStorageObj, $campaign, $userId, $options = [], $apiName = '', $goalIdentifier = '')
    {
        LoggerService::setApiName($apiName);
        $bucketInfo = null;
        $this->hasStoredVariation = false;

        if ($campaign == null) {
            return $bucketInfo;
        }

        $isCampaignPartOfGroup = $this->settings && CampaignUtil::isPartOfGroup($this->settings, $campaign["id"]);
        $campaignKey = $campaign['key'];
        $decision['isUserWhitelisted'] = false;
        $decision['fromUserStorageService'] = false;

        # get new bucketing enabled flag from settings
        if ($this->settings!=null && isset($this->settings["isNB"]) && $this->settings["isNB"]) {
            $is_new_bucketing_enabled = true;
        } else {
            $is_new_bucketing_enabled = false;
        }

        // VWO generated UUID based on passed UserId and Account ID
        if (isset($this->accountId)) {
            $decision['vwoUserId'] = UuidUtil::get($userId, $this->accountId);
        }

        if ($isCampaignPartOfGroup) {
            $groupId = $this->settings["campaignGroups"][$campaign["id"]];
            $decision["groupId"] = $groupId;
            $groupName = $this->settings["groups"][$groupId]["name"];
            $decision["groupName"] = $groupName;
        }

        //check for whitelisting if applied and get Variation Info
        $bucketInfo = CampaignUtil::findVariationFromWhiteListing($campaign, $userId, $options, $is_new_bucketing_enabled);
        // do murmur operations and get Variation for the userId
        if ($bucketInfo == null) {
            if (isset($campaign['isAlwaysCheckSegment'])) {
                $isPreSegmentation = ValidationsUtil::checkPreSegmentation($campaign, $userId, $options);
                $bucketInfo = $this->getVariationIfPreSegmentationApplied($isPreSegmentation, $campaign, $userId, $userStorageObj, $goalIdentifier);
            } else {
                $bucketInfo = $this->userStorageGet($userStorageObj, $userId, $campaign);
                if ($bucketInfo == null) {
                    if (self::checkCampaignNotActivated($apiName, $userStorageObj, $userId, $campaignKey)) {
                        return $bucketInfo;
                    }

                    $isPresegmentation = ValidationsUtil::checkPreSegmentation($campaign, $userId, $options);
                    $isPresegmentationAndTrafficPassed = $isPresegmentation && self::isUserPartOfCampaign($userId, $campaign['percentTraffic'], $campaign, $is_new_bucketing_enabled);
                    if ($isPresegmentationAndTrafficPassed && $isCampaignPartOfGroup) {
                        $groupCampaigns = CampaignUtil::getGroupCampaigns($this->settings, $groupId);

                        if ($groupCampaigns) {
                            $isAnyCampaignWhitelistedOrStored = $this->checkWhitelistingOrStorageForGroupedCampaigns($userStorageObj, $userId, $campaign, $groupCampaigns, $groupName, $options);

                            // Return None as other campaign(s) is/are whitelisted or stored
                            if ($isAnyCampaignWhitelistedOrStored) {
                                LoggerService::log(
                                    Logger::INFO,
                                    'MEG_CALLED_CAMPAIGN_NOT_WINNER',
                                    [
                                        '{userId}' => $userId,
                                        '{campaignKey}' => $campaign["key"],
                                        '{groupName}' => $groupName
                                    ],
                                    self::CLASSNAME
                                );
                                return null;
                            }

                            $eligibleCampaigns = self::getEligibleCampaigns($userId, $groupCampaigns, $campaign, $options, $is_new_bucketing_enabled);
                            $megAlgoNumber = isset($this->settings["groups"][$groupId]["et"]) ? $this->settings["groups"][$groupId]["et"] : self::RandomAlgo ;

                            $nonEligibleCampaignsKey = self::getNonEligibleCampaignsKey($eligibleCampaigns, $groupCampaigns);
                            LoggerService::log(
                                Logger::DEBUG,
                                'MEG_ELIGIBLE_CAMPAIGNS',
                                [
                                    '{userId}' => $userId,
                                    '{eligibleCampaignKeys}' => implode(",", self::getEligibleCampaignsKey($eligibleCampaigns)),
                                    '{inEligibleText}' => "campaigns:" . ($nonEligibleCampaignsKey ? implode(",", $nonEligibleCampaignsKey) : "no campaigns"),
                                    '{groupName}' => $groupName
                                ],
                                self::CLASSNAME
                            );

                            LoggerService::log(
                                Logger::INFO,
                                'MEG_ELIGIBLE_CAMPAIGNS',
                                [
                                    '{userId}' => $userId,
                                    '{noOfEligibleCampaigns}' => count($eligibleCampaigns),
                                    '{noOfGroupCampaigns}' => count($groupCampaigns),
                                    '{groupName}' => $groupName
                                ],
                                self::CLASSNAME
                            );
                            $winnerCampaign = $this->findWinnerCampaign($userId, $eligibleCampaigns, $megAlgoNumber, $groupId, $this->settings);

                            LoggerService::log(
                                Logger::INFO,
                                'MEG_GOT_WINNER_CAMPAIGN',
                                [
                                    '{userId}' => $userId,
                                    '{campaignKey}' => $winnerCampaign["key"],
                                    '{groupName}' => $groupName
                                ],
                                self::CLASSNAME
                            );
                            if ($winnerCampaign && $winnerCampaign["id"] == $campaign["id"]) {
                                $bucketInfo = Bucketer::getBucket($userId, $campaign, $is_new_bucketing_enabled);
                                if ($bucketInfo == null) {
                                    return $bucketInfo;
                                } else {
                                    $this->userStorageSet($userStorageObj, $userId, $campaign['key'], $bucketInfo, $goalIdentifier);
                                }
                            } else {
                                // No winner/variation
                                LoggerService::log(
                                    Logger::INFO,
                                    'MEG_CALLED_CAMPAIGN_NOT_WINNER',
                                    [
                                        '{userId}' => $userId,
                                        '{campaignKey}' => $campaign["key"],
                                        '{groupName}' => $groupName
                                    ],
                                    self::CLASSNAME
                                );
                                return $bucketInfo;
                            }
                        }
                    }

                    if ($bucketInfo == null) {
                        $bucketInfo = $this->getVariationIfPreSegmentationApplied($isPresegmentation, $campaign, $userId, $userStorageObj, $goalIdentifier);
                    }
                } else {
                    $this->hasStoredVariation = true;
                    $decision['fromUserStorageService'] = !!$bucketInfo['name'];
                    LoggerService::log(
                        Logger::INFO,
                        'GOT_STORED_VARIATION',
                        [
                            '{userId}' => $userId,
                            '{variationName}' => $bucketInfo['name'],
                            '{campaignKey}' => $campaign['key']
                        ],
                        self::CLASSNAME
                    );
                }
            }
        } else {
            $decision['isUserWhitelisted'] = true;
        }

        if ($bucketInfo != null) {
            $decision['campaignId'] = $campaign['id'];
            $decision['campaignKey'] = $campaignKey;
            $decision['campaignType'] = $campaign['type'];
            // campaign segmentation conditions
            $decision['customVariables'] = isset($options['customVariables']) ? $options['customVariables'] : [];
            // event name
            $decision['event'] = Hooks::DECISION_TYPES['CAMPAIGN_DECISION'];
            // goal tracked in case of track API
            $decision['goalIdentifier'] = $goalIdentifier;
            // campaign whitelisting flag
            $decision['isForcedVariationEnabled'] = isset($campaign['isForcedVariationEnabled']) ? $campaign['isForcedVariationEnabled'] : false;
            $decision['sdkVersion'] = ImpressionBuilder::SDK_VERSION;
            // API name which triggered the event
            $decision['source'] = $apiName;
            // Passed in API
            $decision['userId'] = $userId;
            // Campaign Whitelisting conditions
            $decision['variationTargetingVariables'] = isset($options['variationTargetingVariables']) ? $options['variationTargetingVariables'] : [];

            if (isset($campaign['name'])) {
                $decision["campaignName"] = $campaign["name"];
            }

            $variationName = $bucketInfo['name'];
            if ($campaign['type'] === CampaignTypes::FEATURE_ROLLOUT) {
                $decision['isFeatureEnabled'] = true;
            } else {
                if ($campaign['type'] === CampaignTypes::FEATURE_TEST) {
                    $decision['isFeatureEnabled'] = $bucketInfo['isFeatureEnabled'];
                }
                $decision['variationName'] = $variationName;
                $decision['variationId'] = $bucketInfo['id'];
            }
            $this->hooksManager->execute($decision);
        }

        return $bucketInfo;
    }

    /***
     * @param  $userStorageObj
     * @param  $userId
     * @param  $campaign
     * @param  bool $disableLogs    optional: disable logs if True
     * @return array|null
     */
    private function userStorageGet($userStorageObj, $userId, $campaign, $disableLogs = false)
    {

        if (!empty($userStorageObj)) {
            $campaignKey = $campaign['key'];
            try {
                $variationInfo = $userStorageObj->get($userId, $campaignKey);
            } catch (Exception $e) {
                $variationInfo = null;
                LoggerService::log(
                    Logger::ERROR,
                    'USER_STORAGE_SERVICE_GET_FAILED',
                    ['{userId}' => $userId, '{error}' => $e->getMessage()],
                    self::CLASSNAME
                );
            }

            if (
                isset($variationInfo['variationName']) && is_string(
                    $variationInfo['variationName']
                ) && !empty($variationInfo['variationName']) && array_key_exists('campaignKey', $variationInfo) && $variationInfo['campaignKey'] == $campaignKey
            ) {
                LoggerService::log(
                    Logger::INFO,
                    'GETTING_DATA_USER_STORAGE_SERVICE',
                    ['{userId}' => $userId, '{campaignKey}' => $campaignKey],
                    self::CLASSNAME,
                    $disableLogs
                );
                if ($campaign !== null) {
                    $bucketInfo = Bucketer::getBucketVariationId(
                        $campaign,
                        $variationInfo['variationName']
                    );
                    if (isset($variationInfo['goalIdentifier'])) {
                        $bucketInfo['goalIdentifier'] = $variationInfo['goalIdentifier'];
                    }
                    return $bucketInfo;
                }
            } else {
                LoggerService::log(
                    Logger::DEBUG,
                    'USER_STORAGE_SERVICE_NO_STORED_DATA',
                    ['{userId}' => $userId, '{campaignKey}' => $campaignKey],
                    self::CLASSNAME,
                    $disableLogs
                );
            }
        } else {
            LoggerService::log(
                Logger::DEBUG,
                'USER_STORAGE_SERVICE_NOT_CONFIGURED',
                [],
                self::CLASSNAME,
                $disableLogs
            );
        }

        return null;
    }

    /**
     * this function will save the data to user-storage
     *
     * @param string $userId
     * @param string $campaignKey
     * @param array  $variation
     * @param string $goalIdentifier
     */
    public function userStorageSet($userStorageObj, $userId, $campaignKey, $variation, $goalIdentifier = '')
    {
        if (!empty($userStorageObj)) {
            $campaignInfo = CommonUtil::getUserCampaignVariationMapping($campaignKey, $variation, $userId, $goalIdentifier);
            try {
                $userStorageObj->set($campaignInfo);
            } catch (Exception $e) {
                LoggerService::log(
                    Logger::ERROR,
                    'USER_STORAGE_SERVICE_GET_FAILED',
                    ['{userId}' => $userId, '{error}' => $e->getMessage()],
                    self::CLASSNAME
                );
            }
            LoggerService::log(
                Logger::INFO,
                'SETTING_DATA_USER_STORAGE_SERVICE',
                ['{userId}' => $userId, '{campaignKey}' => $campaignKey],
                self::CLASSNAME
            );
        } else {
            LoggerService::log(Logger::DEBUG, 'USER_STORAGE_SERVICE_NOT_CONFIGURED', [], self::CLASSNAME);
        }
    }

    /**
     * Finds and returns eligible campaigns from $groupCampaigns.
     *
     * @param  string $userId         the unique ID assigned to User
     * @param  array  $groupCampaigns campaigns part of group
     * @param  array  $calledCampaign campaign for which api is called
     * @param  array  $options        contains variables for segmentation
     * @return array  eligible campaigns from which winner campaign is to be selected
     */
    private static function getEligibleCampaigns($userId, $groupCampaigns, $calledCampaign, $options, $is_new_bucketing_enabled)
    {
        $eligibleCampaigns = [];
        foreach ($groupCampaigns as $campaign) {
            if ($calledCampaign["id"] == $campaign["id"] || ValidationsUtil::checkPreSegmentation($campaign, $userId, $options, true) && self::isUserPartOfCampaign($userId, $campaign['percentTraffic'], $campaign, $is_new_bucketing_enabled)) {
                $eligibleCampaigns[] = $campaign;
            }
        }
        return $eligibleCampaigns;
    }

    /**
     * Evaluates whether the user should become part of campaign or not
     *
     * @param  string    $userId         the unique ID assigned to User
     * @param  int|float $percentTraffic traffic for a campaign in which user is participating
     * @return bool
     */
    private static function isUserPartOfCampaign($userId, $percentTraffic, $campaign, $is_new_bucketing_enabled)
    {
        list($bucketVal, $hashValue) = Bucketer::getBucketVal($userId, $campaign, $is_new_bucketing_enabled, true);
        return Bucketer::isUserPartofCampaign($bucketVal, $percentTraffic);
    }

    /**
     * Finds and returns the winner campaign from $eligibleCampaigns list.
     *
     * @param  string $userId            the unique ID assigned to User
     * @param  array  $eligibleCampaigns campaigns part of group which were eligible to be winner
     * @return array  winner campaign from eligible_campaigns
     */
    private static function findWinnerCampaign($userId, $eligibleCampaigns, $megAlgoNumber, $groupId, $settingsFile)
    {
        # get new bucketing enabled flag from settings
        if ($settingsFile!=null && isset($settingsFile["isNB"]) && $settingsFile["isNB"]) {
            $is_new_bucketing_enabled = true;
        } else {
            $is_new_bucketing_enabled = false;
        }

        if (count($eligibleCampaigns) == 1) {
            return  $eligibleCampaigns[0];
        } else {
            if ($megAlgoNumber == self::RandomAlgo) {
            //Scale the traffic percent of each campaign
                $eligibleCampaigns = CampaignUtil::scaleCampaigns($eligibleCampaigns);
            //Allocate new range for campaigns
                $eligibleCampaigns = Bucketer::addRangesToCampaigns($eligibleCampaigns);
            //Now retrieve the campaign from the modified_campaign_for_whitelisting
                list($bucketVal, $hashValue) = Bucketer::getBucketVal($userId, [], false, true);
                return Bucketer::getCampaignUsingRange($bucketVal, $eligibleCampaigns);
            } else {
                $winnerCampaign = null;

                $found = false; // flag to check whether winnerCampaign has been found or not and helps to break from the outer loop
                $priorityOrder = isset($settingsFile['groups'][$groupId]['p']) ? $settingsFile['groups'][$groupId]['p'] : [];
                $wt = isset($settingsFile['groups'][$groupId]['wt']) ? $settingsFile['groups'][$groupId]['wt'] : [];

                for ($i = 0; $i < count($priorityOrder); $i++) {
                    for ($j = 0; $j < count($eligibleCampaigns); $j++) {
                        if ($eligibleCampaigns[$j]['id'] == $priorityOrder[$i]) {
                            $winnerCampaign = $eligibleCampaigns[$j];
                            $found = true;
                            break;
                        }
                    }
                    if ($found == true) {
                        break;
                    }
                }

                // If winnerCampaign not found through Priority, then go for weighted Random distribution and for that,
                // Store the list of campaigns (participatingCampaigns) out of eligibleCampaigns and their corresponding weights which are present in weightage distribution array (wt) in 2 different lists
                if ($winnerCampaign == null) {
                    $weights = array();
                    $partipatingCampaignList = array();

                    for ($i = 0; $i < count($eligibleCampaigns); $i++) {
                        $campaignId = $eligibleCampaigns[$i]['id'];
                        if (isset($wt[$campaignId])) {
                            $weights[] = $wt[$campaignId];
                            $partipatingCampaignList[] = $eligibleCampaigns[$i];
                        }
                    }

                    /*
                    * Finding winner campaign using weighted random distribution :
                    1. Calculate the sum of all weights
                    2. Generate a random number between 0 and the weight sum:
                    3. Iterate over the weights array and subtract each weight from the random number until the random number becomes negative. The corresponding ith value is the required value
                    4. Set the ith campaign as WinnerCampaign
                    */
                    $weightSum = array_sum($weights);
                    $randomNumber = rand(1, $weightSum);

                    $sum = 0;
                    for ($i = 0; $i < count($weights); $i++) {
                        $sum += $weights[$i];
                        if ($randomNumber < $sum) {
                            $winnerCampaign = $partipatingCampaignList[$i];
                            break;
                        }
                    }
                }

                return $winnerCampaign;
            }
        }
    }

    /**
     * Get campaign keys of all eligibleCampaigns.
     *
     * @param  array $eligibleCampaigns contains eligibleCampaigns
     * @return array campaign keys of all eligibleCampaigns
     */
    private static function getEligibleCampaignsKey($eligibleCampaigns)
    {
        $eligibleCampaignsName = [];
        foreach ($eligibleCampaigns as $campaign) {
            $eligibleCampaignsName[] = $campaign["key"];
        }
        return $eligibleCampaignsName;
    }

    /**
     * get campaign keys of all non eligibleCampaigns.
     *
     * @param  array $eligibleCampaigns contains eligibleCampaigns
     * @param  array $groupCampaigns    contains groupCampaigns
     * @return array campaign keys of all non eligibleCampaigns
     */
    private static function getNonEligibleCampaignsKey($eligibleCampaigns, $groupCampaigns)
    {
        $NonEligibleCampaignsName = [];
        foreach ($groupCampaigns as $groupCampaign) {
            if (!in_array($groupCampaign, $eligibleCampaigns)) {
                $NonEligibleCampaignsName[] = $groupCampaign["key"];
            }
        }
        return $NonEligibleCampaignsName;
    }

    /**
     * Checks if any other campaign in groupCampaigns satisfies whitelisting or is in user storage.
     *
     * @param  object $userStorageObj userStorage object
     * @param  string $userId         the unique ID assigned to User
     * @param  array  $calledCampaign
     * @param  array  $groupCampaigns campaigns part of group
     * @param  string $groupName      group name
     * @param  array  $options        contains variationTargetingVariables
     * @return bool
     */
    private function checkWhitelistingOrStorageForGroupedCampaigns($userStorageObj, $userId, $calledCampaign, $groupCampaigns, $groupName, $options)
    {
        # get new bucketing enabled flag from settings
        if ($this->settings!=null && isset($this->settings["isNB"]) && $this->settings["isNB"]) {
            $is_new_bucketing_enabled = true;
        } else {
            $is_new_bucketing_enabled = false;
        }

        foreach ($groupCampaigns as $campaign) {
            if ($calledCampaign["id"] != $campaign["id"]) {
                $targetedVariation = CampaignUtil::findVariationFromWhiteListing($campaign, $userId, $options, $is_new_bucketing_enabled, true);
                if ($targetedVariation) {
                    LoggerService::log(
                        Logger::INFO,
                        'OTHER_CAMPAIGN_SATISFIES_WHITELISTING_STORAGE',
                        [
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaign["key"],
                            '{groupName}' => $groupName,
                            '{type}' => "whitelisting"
                        ],
                        self::CLASSNAME
                    );
                    return true;
                }
            }
        }

        foreach ($groupCampaigns as $campaign) {
            if ($calledCampaign["id"] != $campaign["id"]) {
                $userStorageData = $this->userStorageGet($userStorageObj, $userId, $campaign, true);
                if ($userStorageData) {
                    LoggerService::log(
                        Logger::INFO,
                        'OTHER_CAMPAIGN_SATISFIES_WHITELISTING_STORAGE',
                        [
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaign["key"],
                            '{groupName}' => $groupName,
                            '{type}' => "user storage"
                        ],
                        self::CLASSNAME
                    );
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks if campaign is activated for track, getVariationName, getFeatureVariableValue API when userStorage is used
     *
     * @param  string $apiName        api name
     * @param  object $userStorageObj userStorage object
     * @param  string $userId         the unique ID assigned to User
     * @param  string $campaignKey    Campaign Key
     * @return bool
     */
    private static function checkCampaignNotActivated($apiName, $userStorageObj, $userId, $campaignKey)
    {
        if (
            in_array($apiName, ['track', 'getVariationName', 'getFeatureVariableValue'])
            && !empty($userStorageObj)
        ) {
            LoggerService::log(
                Logger::WARNING,
                'CAMPAIGN_NOT_ACTIVATED',
                ['{userId}' => $userId, '{campaignKey}' => $campaignKey, '{api}' => $apiName],
                self::CLASSNAME
            );
            LoggerService::log(
                Logger::INFO,
                'CAMPAIGN_NOT_ACTIVATED',
                [
                    '{userId}' => $userId,
                    '{campaignKey}' => $campaignKey,
                    '{reason}' => $apiName === 'track' ? 'track it' : 'get the decision/value'
                ],
                self::CLASSNAME
            );
            return true;
        }
        return false;
    }

    /**
     * Get variation by murmur logic if pre segmentation pass
     *
     * @param  bool   $isPreSegmentation pre-segmentation flag
     * @param  string $userId         the unique ID assigned to User
     * @param  object $userStorageObj userStorage object
     * @param  array  $campaign       campaign data
     * @param  string $goalIdentifier goal Identifier used in track API
     * @return array|null
     */
    private function getVariationIfPreSegmentationApplied($isPreSegmentation, $campaign, $userId, $userStorageObj = null, $goalIdentifier = '')
    {
        $bucketInfo = null;
        //check for pre-segmentation if applied
        if ($isPreSegmentation == false) {
            LoggerService::log(
                Logger::INFO,
                'DECISION_NO_VARIATION_ALLOTED',
                [
                    '{userId}' => $userId,
                    '{campaignKey}' => $campaign['key']
                ],
                self::CLASSNAME
            );

            return $bucketInfo;
        }

        # get new bucketing enabled flag from settings
        if ($this->settings!=null && isset($this->settings["isNB"]) && $this->settings["isNB"]) {
            $is_new_bucketing_enabled = true;
        } else {
            $is_new_bucketing_enabled = false;
        }

        $bucketInfo = Bucketer::getBucket($userId, $campaign, $is_new_bucketing_enabled);
        LoggerService::log(
            Logger::INFO,
            'USER_VARIATION_ALLOCATION_STATUS',
            [
                '{userId}' => $userId,
                '{status}' => $bucketInfo ? 'got variation:' . $bucketInfo['name'] : 'did not get any variation',
                '{campaignKey}' => $campaign['key']
            ],
            self::CLASSNAME
        );
        if ($bucketInfo == null) {
            return $bucketInfo;
        }

        $this->userStorageSet($userStorageObj, $userId, $campaign['key'], $bucketInfo, $goalIdentifier);
        return $bucketInfo;
    }
}
