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

use Monolog\Logger;
use vwo\Core\Bucketer;
use vwo\VWO as VWO;
use vwo\Constants\Constants as Constants;
use Ramsey\Uuid\Uuid;
use vwo\Utils\SegmentEvaluator as SegmentEvaluator;

/***
 * All the common function will be invoked from common  class
 *
 * Class Common
 *
 * @package vwo\Utils
 */
class Common
{

    private static $CLASSNAME = 'vwo\Utils\Common';

    /***
     * basic creation of log message from constants.php
     *
     * @param  $message
     * @param  $params
     * @param  $className
     * @return mixed
     */

    public static function makelogMessage($message, $params, $className = '', $apiName = '')
    {
        $params['{file}'] = $className;
        $response = str_replace(array_keys($params), array_values($params), $message);
        return '[' . $apiName . '] ' . $response;
    }

    /**
     * method to create the input array for user-storage set function
     *
     * @param  $campaignKey
     * @param  $bucketInfo
     * @param  $customerHash
     * @return array
     */

    public static function getUserData($campaignKey, $variation, $userId)
    {
        return [
            'userId' => $userId,
            'variationName' => $variation['name'],
            'campaignKey' => $campaignKey,
        ];
    }


    /**
     * To fetch the goal id using goals array and goal identifier
     *
     * @param  $goals
     * @param  $goalIdentifier
     * @return int
     */
    public static function getGoal($goals, $goalIdentifier)
    {
        $goalData = 0;
        if (count($goals)) {
            foreach ($goals as $goal) {
                if ($goal['identifier'] === $goalIdentifier) {
                    $goalData = $goal;
                    break;
                }
            }
        }
        return $goalData;
    }

    /**
     * @param  $variables
     * @param  $variableKey
     * @return bool|float|int|null|string
     */
    public static function fetchVariableValueFromCampaign($variables, $variableKey)
    {
        $value = null;
        if (count($variables)) {
            foreach ($variables as $variable) {
                if ($variable['key'] == $variableKey) {
                    $value = self::typeCast($variable['value'], $variable['type']);
                    break;
                }
            }
        }
        return $value;
    }

    /**
     * @param  $value
     * @param  $type
     * @return bool|float|int|string
     */
    public static function typeCast($value, $type)
    {
        switch ($type) {
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'boolean':
                return (bool)$value;
            case 'integer':
                return (int)$value;
        }
    }


    public static function findVariationFromWhiteListing($campaign, $userId, $options)
    {
        $bucketInfo = null;
        if (isset($campaign['isForcedVariationEnabled']) && $campaign['isForcedVariationEnabled'] == true) {
            $variationTargetingVariables = Common::getValueFromOptions($options, 'variationTargetingVariables');
            $bucketInfo = Common::getForcedBucket($campaign, $userId, $variationTargetingVariables);
            $status = $bucketInfo != null ? 'satisfy' : "didn't satisfy";
            \vwo\VWO::addLog(Logger::DEBUG, Constants::INFO_MESSAGES['WHITELISTING_ELIGIBILITY_STATUS'], ['{status}' => $status, '{userId}' => $userId, '{variation}' => $status == 'satisfy' ? $bucketInfo['name'] : 'no', '{campaign_key}' => $campaign['key'], '{variation_targeting_variables}' => json_encode($variationTargetingVariables)], self::$CLASSNAME);
        } else {
            \vwo\VWO::addLog(Logger::INFO, Constants::INFO_MESSAGES['WHITELISTING_SKIPPED'], [ '{reason}' => '','{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{variation}' => ''], self::$CLASSNAME);
        }
        return $bucketInfo;
    }

    public static function getForcedBucket($campaign, $userId, $variationTargetingVariables)
    {
        $variationTargetingVariables['_vwoUserId'] = $userId;
        $validVariations = [];
        $totalVariationTraffic = 0;
        $segmentObj = new SegmentEvaluator();
        foreach ($campaign['variations'] as $variation) {
            if (isset($variation['segments']) && count($variation['segments'])) {
                $result = $segmentObj->evaluate($variation['segments'], $variationTargetingVariables);
                if ($result) {
                    $totalVariationTraffic += $variation['weight'];
                    $validVariations[] = $variation;
                }
                \vwo\VWO::addLog(Logger::INFO, Constants::INFO_MESSAGES['SEGMENTATION_STATUS'], [ '{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{segmentationType}' => 'whitelisting','{variation}' => 'for variation:' . $variation['name'],'{status}' => $result === true ? 'passed' : 'failed','{customVariables}' => json_encode($variationTargetingVariables)], self::$CLASSNAME);
            } else {
                \vwo\VWO::addLog(Logger::INFO, Constants::INFO_MESSAGES['WHITELISTING_SKIPPED'], [ '{reason}' => 'segment was missing, hence','{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{variation}' => 'for variation:' . $variation['name']], self::$CLASSNAME);
            }
        }
        $totalValidVariations = count($validVariations);
        if ($totalValidVariations == 1) {
            return $validVariations[0];
        } elseif ($totalValidVariations > 1) {
            return self::evaluateBestVariation($validVariations, $totalVariationTraffic, $userId);
        }
        return null;
    }

    /**
     * @param  $validVariations
     * @param  $totalVariationTraffic
     * @param  $userId
     * @return null| array of variation
     */
    public static function evaluateBestVariation($validVariations, $totalVariationTraffic, $userId)
    {
        //scale and assign ranges to the variations
        $validVariations = self::scaleVariations($validVariations, $totalVariationTraffic);
        $validVariations = Bucketer::addRangesToVariations($validVariations);
        //find murmur
        $bucketVal = Bucketer::getBucketVal($userId, Bucketer::$MAX_CAMPAIGN_TRAFFIC);
        //get range according to murmur
        $rangeForVariation = Bucketer::getRangeForVariations($bucketVal);
        //get variation
        $variation = Bucketer::variationUsingRange($rangeForVariation, $validVariations);
        //return final variation assigned to the user
        return $variation;
    }

    public static function scaleVariations($variations, $totalVariationTraffic)
    {
        $avgWeight = 0;
        if ($totalVariationTraffic == 0) {
            $variationCount = count($variations);
            $avgWeight = 100 / $variationCount;
        }
        foreach ($variations as $key => $variation) {
            if ($avgWeight > 0) {
                $newWeight = $avgWeight;
            } else {
                $newWeight = ($variation['weight'] / $totalVariationTraffic) * 100;
            }
            $variations[$key]['weight'] = $newWeight;
        }
        return $variations;
    }

    /**
     * @param  $variations
     * @return null
     */
    public static function fetchControlVariation($variations)
    {
        $returnVariation = null;
        if (count($variations)) {
            foreach ($variations as $variation) {
                if ($variation['id'] == 1) {
                    $returnVariation = $variation;
                    break;
                }
            }
        }
        return $returnVariation;
    }

    /**
     * @param  $accountid
     * @param  $userId
     * @param  array     $params
     * @return array
     */
    public static function mergeCommonQueryParams($accountid, $userId, $params = [])
    {
        $params['account_id'] = $accountid;
        $params['ap'] = 'server';
        $params['uId'] = $userId;
        $params['sId'] = time();
        $params['u'] = self::getUUId5($userId, $accountid);
        $params['sdk-v'] = Constants::SDK_VERSION;
        $params['sdk'] = Constants::SDK_LANGUAGE;
        return $params;
    }

    /**
     * @param  $userId
     * @param  $accountId
     * @return string
     */
    public static function getUUId5($userId, $accountId)
    {
        $uuid = '';
        try {
            $uuid5_seed = Uuid::uuid5(Uuid::NAMESPACE_URL, Constants::UUID_SEED);
            $uuid5_seed_accountId = Uuid::uuid5($uuid5_seed, $accountId);
            $uuid5 = Uuid::uuid5($uuid5_seed_accountId, $userId);
            $uuid = strtoupper(str_replace('-', '', $uuid5->toString()));
            VWO::addLog(Logger::DEBUG, Constants::DEBUG_MESSAGES['UUID_FOR_USER'], ['{userid}' => $userId, '{accountId}' => $accountId, '{desiredUuid}' => $uuid], self::$CLASSNAME);
        } catch (UnsatisfiedDependencyException $e) {
            self::addLog(Logger::ERROR, 'UnsatisfiedDependencyException : ' . $e->getMessage());
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
        return $uuid;
    }

    /**
     * @param  $obj
     * @return array
     */
    public static function getKeyValue($obj)
    {
        $key = key($obj);
        $value = isset($obj[$key]) ? $obj[$key] : null;
        return [$key, $value];
    }

    /**
     * Get value from options using the option name
     *
     * @param  $options
     * @param  $optionName
     * @return $optionValue |null
     */
    public static function getValueFromOptions($options, $optionName)
    {
        if (array_key_exists($optionName, $options)) {
            return $options[$optionName];
        }
        switch ($optionName) {
            case 'customVariables':
                return [];
            case 'variationTargetingVariables':
                return [];
        }
        return null;
    }
}
