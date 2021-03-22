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
     * @param $campaign
     * @param $usesrId
     * @param array $options
     * @return array|mixed|null
     */
    public function fetchVariationData($userStorageObj, $campaign, $userId, $options = [], $apiName = '', $goalIdentifier = '')
    {
        LoggerService::setApiName($apiName);
        $bucketInfo = null;

        if ($campaign == null) {
            return $bucketInfo;
        }

        $campaignKey = $campaign['key'];
        $decision['isUserWhitelisted'] = false;
        $decision['fromUserStorageService'] = false;

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
                if (
                    in_array($apiName, ['track', 'getVariationName', 'getFeatureVariableValue']) &&
                    !empty($userStorageObj) &&
                    $campaign['type'] != CampaignTypes::FEATURE_ROLLOUT
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

                //check for pre-segmentation if applied
                $result = ValidationsUtil::checkPreSegmentation($campaign, $userId, $options);
                if ($result == false) {
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
            if ($campaign['type'] === CampaignTypes::AB) {
                $decision['isUserWhitelisted'] = !!$bucketInfo['name'];
            } elseif ($campaign['type'] === CampaignTypes::FEATURE_TEST) {
                $decision['isUserWhitelisted'] = $bucketInfo['isFeatureEnabled'];
            }
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
     * @return array|null
     */
    private function userStorageGet($userStorageObj, $userId, $campaign)
    {

        if (!empty($userStorageObj)) {
            $campaignKey = $campaign['key'];
            $variationInfo = $userStorageObj->get($userId, $campaignKey);
            if (
                isset($variationInfo['variationName']) && is_string(
                    $variationInfo['variationName']
                ) && !empty($variationInfo['variationName']) && array_key_exists('campaignKey', $variationInfo) && $variationInfo['campaignKey'] == $campaignKey
            ) {
                LoggerService::log(
                    Logger::INFO,
                    LogMessages::INFO_MESSAGES['GETTING_UP_USER_STORAGE_SERVICE'],
                    ['{userId}' => $userId]
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
                LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['GET_USER_STORAGE_SERVICE_FAILED'], ['{userId}' => $userId]);
            }
        } else {
            LoggerService::log(Logger::DEBUG, LogMessages::DEBUG_MESSAGES['NO_USER_STORAGE_SERVICE_GET'], ['{userId}' => $userId]);
        }

        return null;
    }

    /**
     * this function will save the data to user-storage
     * @param string $userId
     * @param string $campaignKey
     * @param array $variation
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
}
