<?php

/**
 * Copyright 2019-2021 Wingify Software Pvt. Ltd.
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
use vwo\Constants\LogMessages as LogMessages;
use vwo\Utils\ImpressionBuilder;
use vwo\Utils\UuidUtil;
use vwo\Utils\Validations as ValidationsUtil;

class VariationDecider
{
    public $hasStoredVariation;
    private $accountId;
    private $hooksManager;
    private $settings;

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

        if ($campaign == null) {
            return $bucketInfo;
        }

        $isCampaignPartOfGroup = $this->settings && CampaignUtil::isPartOfGroup($this->settings, $campaign["id"]);
        $campaignKey = $campaign['key'];
        $decision['isUserWhitelisted'] = false;
        $decision['fromUserStorageService'] = false;

        if($isCampaignPartOfGroup) {
            $groupId = $this->settings["campaignGroups"][$campaign["id"]];
            $decision["groupId"] = $groupId;
            $groupName = $this->settings["groups"][$groupId]["name"];
            $decision["groupName"] = $groupName;
        }

        //check for whitelisting if applied and get Variation Info
        $bucketInfo = CampaignUtil::findVariationFromWhiteListing($campaign, $userId, $options);
        // do murmur operations and get Variation for the userId
        if ($bucketInfo == null) {
            $bucketInfo = $this->userStorageGet($userStorageObj, $userId, $campaign);
            if ($bucketInfo == null) {
                LoggerService::log(
                    Logger::DEBUG,
                    LogMessages::DEBUG_MESSAGES['NO_STORED_VARIATION'],
                    ['{userId}' => $userId, '{campaignKey}' => $campaignKey]
                );
                if (in_array($apiName, ['track', 'getVariationName', 'getFeatureVariableValue'])
                    && !empty($userStorageObj)
                ) {
                    LoggerService::log(
                        Logger::DEBUG,
                        LogMessages::DEBUG_MESSAGES['CAMPAIGN_NOT_ACTIVATED'],
                        ['{userId}' => $userId, '{campaignKey}' => $campaignKey, '{api}' => $apiName]
                    );
                    LoggerService::log(
                        Logger::INFO,
                        LogMessages::INFO_MESSAGES['CAMPAIGN_NOT_ACTIVATED'],
                        ['{userId}' => $userId, '{campaignKey}' => $campaignKey, '{reason}' => $apiName === 'track' ? 'track it' : 'get the decision/value']
                    );
                    return $bucketInfo;
                }

                $isPresegmentation = ValidationsUtil::checkPreSegmentation($campaign, $userId, $options);
                $isPresegmentationAndTrafficPassed = $isPresegmentation && self::isUserPartofCampaign($userId, $campaign['percentTraffic']);
                if($isPresegmentationAndTrafficPassed && $isCampaignPartOfGroup) {
                    $groupCampaigns = CampaignUtil::getGroupCampaigns($this->settings, $groupId);

                    if($groupCampaigns) {
                        $isAnyCampaignWhitelistedOrStored = $this->checkWhitelistingOrStorageForGroupedCampaigns($userStorageObj, $userId, $campaign, $groupCampaigns, $groupName, $options);

                        // Return None as other campaign(s) is/are whitelisted or stored
                        if($isAnyCampaignWhitelistedOrStored) {
                            LoggerService::log(
                                Logger::INFO,
                                LogMessages::INFO_MESSAGES['CALLED_CAMPAIGN_NOT_WINNER'],
                                [
                                    '{userId}' => $userId,
                                    '{campaignKey}' => $campaign["key"],
                                    '{name}' => $groupName
                                ]
                            );
                            return null;
                        }

                        $eligibleCampaigns = self::getEligibleCampaigns($userId, $groupCampaigns, $campaign, $options);

                        $nonEligibleCampaignsKey = self::getNonEligibleCampaignsKey($eligibleCampaigns, $groupCampaigns);
                        LoggerService::log(
                            Logger::DEBUG,
                            LogMessages::DEBUG_MESSAGES['GOT_ELIGIBLE_CAMPAIGNS'],
                            [
                                '{userId}' => $userId,
                                '{eligibleCampaignsKey}' => implode(",", self::getEligibleCampaignsKey($eligibleCampaigns)),
                                '{ineligibleCampaignsLogText}' => "campaigns:" . ($nonEligibleCampaignsKey ? implode(",", $nonEligibleCampaignsKey) : "no campaigns"),
                                '{name}' => $groupName
                            ]
                        );

                        LoggerService::log(
                            Logger::INFO,
                            LogMessages::INFO_MESSAGES['GOT_ELIGIBLE_CAMPAIGNS'],
                            [
                                '{userId}' => $userId,
                                '{noOfEligibleCampaigns}' => count($eligibleCampaigns),
                                '{noOfGroupCampaigns}' => count($groupCampaigns),
                                '{name}' => $groupName
                            ]
                        );

                        $winnerCampaign = $this->findWinnerCampaign($userId, $eligibleCampaigns);

                        LoggerService::log(
                            Logger::INFO,
                            LogMessages::INFO_MESSAGES['GOT_WINNER_CAMPAIGN'],
                            [
                                '{userId}' => $userId,
                                '{campaignKey}' => $winnerCampaign["key"],
                                '{name}' => $groupName
                            ]
                        );
                        if($winnerCampaign && $winnerCampaign["id"] == $campaign["id"]) {
                            $bucketInfo = Bucketer::getBucket($userId, $campaign);
                            if($bucketInfo == null) {
                                return $bucketInfo;
                            } else {
                                $this->userStorageSet($userStorageObj, $userId, $campaign['key'], $bucketInfo, $goalIdentifier);
                            }
                        } else {
                            // No winner/variation
                            LoggerService::log(
                                Logger::INFO,
                                LogMessages::INFO_MESSAGES['CALLED_CAMPAIGN_NOT_WINNER'],
                                [
                                    '{userId}' => $userId,
                                    '{campaignKey}' => $campaign["key"],
                                    '{name}' => $groupName
                                ]
                            );
                            return $bucketInfo;
                        }
                    }
                }

                if($bucketInfo == null) {
                    //check for pre-segmentation if applied
                    if ($isPresegmentation == false) {
                        LoggerService::log(
                            Logger::INFO,
                            LogMessages::INFO_MESSAGES['INVALID_VARIATION_KEY'],
                            [
                                '{userId}' => $userId,
                                '{campaignKey}' => $campaign['key']
                            ]
                        );

                        return $bucketInfo;
                    }

                    $bucketInfo = Bucketer::getBucket($userId, $campaign);

                    if ($bucketInfo == null) {
                        return $bucketInfo;
                    }

                    $this->userStorageSet($userStorageObj, $userId, $campaign['key'], $bucketInfo, $goalIdentifier);
                }
            } else {
                $this->hasStoredVariation = true;
                $decision['fromUserStorageService'] = !!$bucketInfo['name'];
                LoggerService::log(
                    Logger::DEBUG,
                    LogMessages::DEBUG_MESSAGES['GETTING_STORED_VARIATION'],
                    [
                        '{userId}' => $userId,
                        '{variationName}' => $bucketInfo['name'],
                        '{campaignKey}' => $campaign['key']
                    ]
                );
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
            // VWO generated UUID based on passed UserId and Account ID
            if (isset($this->accountId)) {
                $decision['vwoUserId'] = UuidUtil::get($userId, $this->accountId);
            }

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
     * @param  $userId
     * @param  $campaignKey
     * @param  bool $disableLogs optional: disable logs if True
     * @return array|null
     */
    private function userStorageGet($userStorageObj, $userId, $campaign, $disableLogs = false)
    {

        if (!empty($userStorageObj)) {
            $campaignKey = $campaign['key'];
            $variationInfo = $userStorageObj->get($userId, $campaignKey);
            if (isset($variationInfo['variationName']) && is_string(
                $variationInfo['variationName']
            ) && !empty($variationInfo['variationName']) && array_key_exists('campaignKey', $variationInfo) && $variationInfo['campaignKey'] == $campaignKey
            ) {
                LoggerService::log(
                    Logger::INFO,
                    LogMessages::INFO_MESSAGES['GETTING_UP_USER_STORAGE_SERVICE'],
                    ['{userId}' => $userId],
                    '',
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
                LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['GET_USER_STORAGE_SERVICE_FAILED'], ['{userId}' => $userId], '', $disableLogs);
            }
        } else {
            LoggerService::log(Logger::DEBUG, LogMessages::DEBUG_MESSAGES['NO_USER_STORAGE_SERVICE_GET'], ['{userId}' => $userId], '', $disableLogs);
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
            $userStorageObj->set($campaignInfo);
            LoggerService::log(
                Logger::INFO,
                LogMessages::INFO_MESSAGES['SETTING_DATA_USER_STORAGE_SERVICE'],
                ['{userId}' => $userId]
            );
        } else {
            LoggerService::log(Logger::DEBUG, LogMessages::DEBUG_MESSAGES['NO_USER_STORAGE_SERVICE_SET']);
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
    private static function getEligibleCampaigns($userId, $groupCampaigns, $calledCampaign, $options)
    {
        $eligibleCampaigns = [];
        foreach ($groupCampaigns as $campaign) {
            if($calledCampaign["id"] == $campaign["id"] || ValidationsUtil::checkPreSegmentation($campaign, $userId, $options, true) && self::isUserPartofCampaign($userId, $campaign['percentTraffic'])) {
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
    private static function isUserPartofCampaign($userId, $percentTraffic)
    {
        $bucketVal = Bucketer::getBucketVal($userId);
        return Bucketer::isUserPartofCampaign($bucketVal, $percentTraffic);
    }

    /**
     * Finds and returns the winner campaign from $eligibleCampaigns list.
     *
     * @param  string $userId            the unique ID assigned to User
     * @param  array  $eligibleCampaigns campaigns part of group which were eligible to be winner
     * @return array  winner campaign from eligible_campaigns
     */
    private static function findWinnerCampaign($userId, $eligibleCampaigns)
    {
        if(count($eligibleCampaigns) == 1) {
            return  $eligibleCampaigns[0];
        }

        //Scale the traffic percent of each campaign
        $eligibleCampaigns = CampaignUtil::scaleCampaigns($eligibleCampaigns);
        //Allocate new range for campaigns
        $eligibleCampaigns = Bucketer::addRangesToCampaigns($eligibleCampaigns);
        //Now retrieve the campaign from the modified_campaign_for_whitelisting
        $bucketVal = Bucketer::getBucketVal($userId);
        return Bucketer::getCampaignUsingRange($bucketVal, $eligibleCampaigns);
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
            if(!in_array($groupCampaign, $eligibleCampaigns)) {
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
        foreach ($groupCampaigns as $campaign) {
            if($calledCampaign["id"] != $campaign["id"]) {
                $targetedVariation = CampaignUtil::findVariationFromWhiteListing($campaign, $userId, $options, true);
                if($targetedVariation) {
                    LoggerService::log(
                        Logger::INFO,
                        LogMessages::INFO_MESSAGES['OTHER_CAMPAIGN_SATISFIES_WHITELISTING_STORAGE'],
                        [
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaign["key"],
                            '{name}' => $groupName,
                            '{type}' => "whitelisting"
                        ]
                    );
                    return true;
                }
            }
        }

        foreach ($groupCampaigns as $campaign) {
            if($calledCampaign["id"] != $campaign["id"]) {
                $userStorageData = $this->userStorageGet($userStorageObj, $userId, $campaign, true);
                if($userStorageData) {
                    LoggerService::log(
                        Logger::INFO,
                        LogMessages::INFO_MESSAGES['OTHER_CAMPAIGN_SATISFIES_WHITELISTING_STORAGE'],
                        [
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaign["key"],
                            '{name}' => $groupName,
                            '{type}' => "user storage"
                        ]
                    );
                    return true;
                }
            }
        }
        return false;
    }
}
