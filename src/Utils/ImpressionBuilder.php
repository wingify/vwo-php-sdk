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

namespace vwo\Utils;

use Monolog\Logger as Logger;
use vwo\Constants\EventEnum;
use vwo\Constants\FileNameEnum;
use vwo\Services\LoggerService as LoggerService;
use vwo\Utils\UuidUtil;
use vwo\Utils\Common as CommonUtil;
use vwo\Constants\Visitor as VisitorConstants;

class ImpressionBuilder
{
    /**
     * sdk version for api hit
     */
    const SDK_VERSION = '1.64.0';
    /**
     * sdk langauge for api hit
     */
    const SDK_LANGUAGE = 'php';

    const CLASSNAME = FileNameEnum::IMPRESSION_BUILDER;

    public static function getVisitorQueryParams($accountId, $campaign, $userId, $combination, $sdkKey, $visitorUserAgent = '', $userIpAddress = '')
    {
        $params = array(
            'ed' => '{"p":"server"}',
        );

        $params = self::mergeCommonTrackingQueryParams(
            $accountId,
            $campaign,
            $userId,
            $combination,
            $params,
            $sdkKey,
            $visitorUserAgent,
            $userIpAddress
        );

        return $params;
    }

    public static function getConversionQueryParams($accountId, $campaign, $userId, $combination, $goal, $revenueValue, $sdkKey, $visitorUserAgent = '', $userIpAddress = '')
    {
        $params = array(
            'goal_id' => $goal['id']
        );

        if (
            $goal['type'] == "REVENUE_TRACKING" && (is_string($revenueValue) || is_float(
                $revenueValue
            ) || is_int($revenueValue))
        ) {
            $params['r'] = $revenueValue;
        }

        $params = self::mergeCommonTrackingQueryParams(
            $accountId,
            $campaign,
            $userId,
            $combination,
            $params,
            $sdkKey,
            $visitorUserAgent,
            $userIpAddress
        );

        return $params;
    }

    public static function getSettingsFileQueryParams($accountId, $sdkKey)
    {
        $params = array(
            'a' => $accountId,
            'i' => $sdkKey,
            'r' => CommonUtil::getRandomNumber(),
            'platform' => 'server',
            'api-version' => 1
        );

        $params = self::mergeCommonQueryParams($params);
        unset($params['env']);

        return $params;
    }

    public static function getPushQueryParams($accountId, $userId, $sdkKey, $tagKey, $tagValue)
    {
        $params = [
            'tags' => '{"u":{"' . $tagKey . '":"' . $tagValue . '"}}'
        ];

        $params = self::mergeTrackingCallParams($accountId, $userId, $params);
        $params = self::mergeCommonQueryParams($params, $sdkKey);

        return $params;
    }

    /**
     *
     * @param $accountId
     * @param $userId
     * @param array $params    - tomerge with
     *
     * @return array
     */
    public static function mergeCommonTrackingQueryParams($accountId, $campaign, $userId, $combination, $params = [], $sdkKey = '', $visitorUserAgent = '', $userIpAddress = '')
    {
        $params['experiment_id'] = $campaign['id'];
        $params['combination'] = $combination; // variation id
        $params['ap'] = 'server';
        $params[VisitorConstants::USER_AGENT] = $visitorUserAgent;
        $params[VisitorConstants::IP] = $userIpAddress;

        $params = self::mergeTrackingCallParams($accountId, $userId, $params);
        $params = self::mergeCommonQueryParams($params, $sdkKey);

        return $params;
    }

    public static function mergeCommonQueryParams($params = [], $sdkKey = '')
    {
        $params['sdk-v'] = self::SDK_VERSION;
        $params['sdk'] = self::SDK_LANGUAGE;
        if ($sdkKey) {
            $params['env'] = $sdkKey;
        }

        return $params;
    }

    public static function mergeTrackingCallParams($accountId, $userId, $params = [])
    {
        $params['account_id'] = $accountId;
        $params['sId'] = time();
        $params['u'] = UuidUtil::get($userId, $accountId, true);

        $params['random'] = time() / 10;

        return $params;
    }

    /**
     * Builds generic properties for different tracking calls required by VWO servers.
     *
     * @param  int    $accountId
     * @param  String $sdkKey
     * @param  String $eventName
     * @param  array  $usageStats
     * @return array $properties
     */
    public static function getEventsBaseProperties($accountId, $sdkKey, $eventName, $visitorUserAgent = '', $userIpAddress = '', $usageStats = [])
    {
         $properties = [
             "en" => $eventName,
             "a" => $accountId,
             "env" => $sdkKey,
             "eTime" => CommonUtil::getCurrentUnixTimestampInMillis(),
             "random" => CommonUtil::getRandomNumber(),
             "p" => "FS",
             VisitorConstants::USER_AGENT => $visitorUserAgent,
             VisitorConstants::IP => $userIpAddress
         ];
         if ($eventName == EventEnum::VWO_VARIATION_SHOWN) {
             $properties = array_merge($properties, $usageStats);
         }
         return $properties;
    }

    /**
     * Builds generic payload required by all the different tracking calls.
     *
     * @param  array  $configObj  setting-file
     * @param  String $userId
     * @param  String $eventName
     * @param  array  $usageStats
     * @return array $properties
     */
    public static function getEventBasePayload($configObj, $userId, $eventName, $usageStats = [])
    {
        $uuid = UuidUtil::get($userId, $configObj["accountId"], true);
        $sdkKey = $configObj["sdkKey"];

        $props = [
            'vwo_sdkName' => self::SDK_LANGUAGE,
            'vwo_sdkVersion' => self::SDK_VERSION,
            'vwo_envKey' => $sdkKey
        ];

        //        if ($usageStats) {
        //            $props = array_merge($props, $usageStats);
        //        }

        $properties = [
            "d" => [
                "msgId" => $uuid . "-" . CommonUtil::getCurrentUnixTimestampInMillis(),
                "visId" => $uuid,
                "sessionId" => time(),
                "event" => [
                  "props" => $props,
                  "name" => $eventName,
                  "time" => CommonUtil::getCurrentUnixTimestampInMillis()
                ],
                "visitor" => [
                  "props" => [
                    "vwo_fs_environment" => $sdkKey
                  ]
                ]
            ]
        ];

        return $properties;
    }

    /**
     * Builds payload to track the visitor.
     *
     * @param  array  $configObj   setting-file
     * @param  String $userId
     * @param  String $eventName
     * @param  int    $campaignId
     * @param  int    $variationId
     * @param  array  $usageStats
     * @return array $properties
     */
    public static function getTrackUserPayloadData($configObj, $userId, $eventName, $campaignId, $variationId, $usageStats = [])
    {
        $properties = self::getEventBasePayload($configObj, $userId, $eventName);

        $properties["d"]["event"]["props"]["id"] = $campaignId;
        $properties["d"]["event"]["props"]["variation"] = $variationId;

        // this is currently required by data-layer team, we can make changes on DACDN and remove it from here
        $properties["d"]["event"]["props"]["isFirst"] = 1;

        LoggerService::log(
            Logger::DEBUG,
            'IMPRESSION_FOR_EVENT_ARCH_TRACK_USER',
            [
                '{accountId}' => $configObj["accountId"],
                '{userId}' => $userId,
                '{campaignId}' => $campaignId,
            ],
            self::CLASSNAME
        );

        return $properties;
    }

    /**
     * Builds payload to track the Goal.
     *
     * @param  array  $configObj    setting-file
     * @param  String $userId
     * @param  String $eventName
     * @param  int    $revenueValue
     * @param  array  $metricMap
     * @param  array  $revenueProps
     * @return array $properties
     */
    public static function getTrackGoalPayloadData(
        $configObj,
        $userId,
        $eventName,
        $metricMap,
        $eventProperties
    ) {
        $properties = self::getEventBasePayload($configObj, $userId, $eventName);

        $metric = [];
        foreach ($metricMap as $campaignId => $goalId) {
            $metric["id_$campaignId"] = ["g_$goalId"];
            LoggerService::log(
                Logger::DEBUG,
                'IMPRESSION_FOR_EVENT_ARCH_TRACK_GOAL',
                [
                    '{goalName}' => $eventName,
                    '{accountId}' => $configObj["accountId"],
                    '{userId}' => $userId,
                    '{campaignId}' => $campaignId
                ],
                self::CLASSNAME
            );
        }

        $properties["d"]["event"]["props"]["vwoMeta"] = [
            "metric" => $metric
        ];

        if (is_array($eventProperties) || is_object($eventProperties)) {
            foreach ($eventProperties as $eventProp => $eventValue) {
                $properties["d"]["event"]["props"][$eventProp] = $eventValue;
            }
        }

        /* if (count($revenueProps) && $revenueValue) {
            foreach ($revenueProps as $revenueProp) {
                $properties["d"]["event"]["props"]["vwoMeta"][$revenueProp] = $revenueValue;
            }
        } */

        $properties['d']['event']['props']['isCustomEvent'] = true;

        return $properties;
    }

    /**
     * Builds payload to apply post segmentation on VWO campaign reports.
     *
     * @param  array  $configObj          setting-file
     * @param  String $userId
     * @param  String $eventName
     * @param  array  $customDimensionMap
     * @return array $properties
     */
    public static function getPushPayloadData($configObj, $userId, $eventName, $customDimensionMap = [])
    {
        $properties = self::getEventBasePayload($configObj, $userId, $eventName);

        $properties['d']['event']['props']['isCustomEvent'] = true;
        foreach ($customDimensionMap as $key => $value) {
            $properties['d']['visitor']['props'][$key] = $value;
        }

        LoggerService::log(
            Logger::DEBUG,
            'IMPRESSION_FOR_EVENT_ARCH_PUSH',
            [
                '{accountId}' => $configObj["accountId"],
                '{userId}' => $userId,
                '{property}' => json_encode($customDimensionMap)
            ],
            self::CLASSNAME
        );

        return $properties;
    }

    /**
     * Builds postData for multiple custom dimension for batch events call
     *
     * @param  integer  $accountId
     * @param  String   $userId
     * @param  array    $customDimensionMap
     * @return array
     */
    public static function getPushBatchEventData($accountId, $userId, $customDimensionMap)
    {
        $data = [];
        $currentTimeStamp = time();
        $uuid = UuidUtil::get($userId, $accountId, true);
        foreach ($customDimensionMap as $tagKey => $tagValue) {
            $data["ev"][] = [
                "eT" => 3,
                "u" => $uuid,
                "t" => urlencode('{"u":{"' . $tagKey . '":"' . $tagValue . '"}}'),
                "sId" => $currentTimeStamp
            ];
        }
        return $data;
    }

    /**
     * Builds query params for batch events call
     *
     * @param integer $accountId
     * @param string $sdkKey
     * @param array $usageStats
     * @return array
     */
    public static function getBatchEventQueryParams($accountId, $sdkKey, $usageStats = [])
    {
        $params = [
            "a" => $accountId,
            "sd" => self::SDK_LANGUAGE,
            "sv" => self::SDK_VERSION,
            "env" => $sdkKey
        ];

        return array_merge($params, $usageStats);
    }

    /**
     * Builds postData for tracking multiple goals for batch events call
     *
     * @param integer $accountId
     * @param String  $userId
     * @param integer $campaignId
     * @param integer $variationId
     * @param array   $goal
     * @param string|float|integer|null $revenue
     * @return array
     */
    public static function getTrackBatchEventData($accountId, $userId, $campaignId, $variationId, $goal, $revenue = null)
    {
        $currentTimeStamp = time();
        $uuid = UuidUtil::get($userId, $accountId, true);
        $data = [
            "eT" => 2,
            "e" => $campaignId,
            "c" => $variationId,
            "g" => $goal['id'],
            "u" => $uuid,
            "sId" => $currentTimeStamp
        ];

        if ($goal['type'] == "REVENUE_TRACKING" && $revenue) {
            $data["r"] = $revenue;
        }

        return $data;
    }
}
