<?php

/**
 * Copyright 2019-2020 Wingify Software Pvt. Ltd.
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

use vwo\Utils\UuidUtil;
use vwo\Utils\Common as CommonUtil;

class ImpressionBuilder
{
    /**
     * sdk version for api hit
     */
    const SDK_VERSION = '1.10.0';
    /**
     * sdk langauge for api hit
     */
    const SDK_LANGUAGE = 'php';

    public static function getVisitorQueryParams($accountId, $campaign, $userId, $combination)
    {
        $params = array(
            'ed' => '{"p":"server"}',
        );

        $params = self::mergeCommonTrackingQueryParams(
            $accountId,
            $campaign,
            $userId,
            $combination,
            $params
        );

        return $params;
    }

    public static function getConversionQueryParams($accountId, $campaign, $userId, $combination, $goal, $revenueValue)
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
            $params
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

        return $params;
    }

    public static function getPushQueryParams($accountId, $userId, $tagKey, $tagValue)
    {
        $params = array(
            'tags' => '{"u":{"' . $tagKey . '":"' . $tagValue . '"}}'
        );

        $params = self::mergeTrackingCallParams($accountId, $userId, $params);
        $params = self::mergeCommonQueryParams($params);

        return $params;
    }

    /**
     *
     * @param $accountId
     * @param $userId
     * @param array $params - tomerge with
     *
     * @return array
     */
    public static function mergeCommonTrackingQueryParams($accountId, $campaign, $userId, $combination, $params = [])
    {
        $params['experiment_id'] = $campaign['id'];
        $params['combination'] = $combination; // variation id
        $params['ap'] = 'server';

        $params = self::mergeTrackingCallParams($accountId, $userId, $params);
        $params = self::mergeCommonQueryParams($params);

        return $params;
    }

    public static function mergeCommonQueryParams($params = [])
    {
        $params['sdk-v'] = self::SDK_VERSION;
        $params['sdk'] = self::SDK_LANGUAGE;

        return $params;
    }

    public static function mergeTrackingCallParams($accountId, $userId, $params = [])
    {
        $params['account_id'] = $accountId;
        $params['uId'] = $userId;
        $params['sId'] = time();
        $params['u'] = UuidUtil::get($userId, $accountId);

        $params['random'] = time() / 10;

        return $params;
    }
}
