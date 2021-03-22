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

namespace vwo\Utils;

use Monolog\Logger;
use vwo\Core\Bucketer;
use vwo\Services\LoggerService;
use vwo\Utils\Common as CommonUtil;
use vwo\Constants\Constants as Constants;
use vwo\Constants\LogMessages as LogMessages;
use vwo\Utils\SegmentEvaluator as SegmentEvaluator;

/***
 * All the common function will be invoked from common  class
 *
 * Class Common
 *
 * @package vwo\Utils
 */
class Campaign
{

    private static $CLASSNAME = 'vwo\Utils\Campaign';
    /**
     * to set the range on the setting level means to each and every campaign
     * @param  array $settings
     * @return array
     * @throws \Exception
     */
    public static function makeRanges($settings = [])
    {
        if (isset($settings['campaigns']) && count($settings['campaigns'])) {
            foreach ($settings['campaigns'] as $key => $campaign) {
                $settings['campaigns'][$key]['variations'] = Bucketer::addRangesToVariations($campaign['variations']);
            }
        }

        return $settings;
    }

    /**
     * if the whitelisting condition get satisfied then this function
     * will evaluate which variation is assigned as per conditions
     * @param $campaign
     * @param $userId
     * @param $options
     * @return array|null
     */
    public static function findVariationFromWhiteListing($campaign, $userId, $options)
    {
        $bucketInfo = null;
        if (isset($campaign['isForcedVariationEnabled']) && $campaign['isForcedVariationEnabled'] == true) {
            $variationTargetingVariables = CommonUtil::getValueFromOptions($options, 'variationTargetingVariables');
            $bucketInfo = self::getForcedBucket($campaign, $userId, $variationTargetingVariables);
            $status = $bucketInfo != null ? 'satisfy' : "didn't satisfy";

            LoggerService::log(Logger::DEBUG, LogMessages::INFO_MESSAGES['WHITELISTING_ELIGIBILITY_STATUS'], ['{status}' => $status, '{userId}' => $userId, '{variation}' => $status == 'satisfy' ? $bucketInfo['name'] : 'no', '{campaign_key}' => $campaign['key'], '{variation_targeting_variables}' => json_encode($variationTargetingVariables)], self::$CLASSNAME);
        } else {
            LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['WHITELISTING_SKIPPED'], [ '{reason}' => '','{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{variation}' => ''], self::$CLASSNAME);
        }
        return $bucketInfo;
    }

    /**
     * this function will evaluate the bucket out of
     * the campaign for whitelisted campaign
     * @param $campaign
     * @param $userId
     * @param $variationTargetingVariables
     * @return array|null
     */
    private static function getForcedBucket($campaign, $userId, $variationTargetingVariables)
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
                LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['SEGMENTATION_STATUS'], [ '{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{segmentationType}' => 'whitelisting','{variation}' => 'for variation:' . $variation['name'],'{status}' => $result === true ? 'passed' : 'failed','{customVariables}' => json_encode($variationTargetingVariables)], self::$CLASSNAME);
            } else {
                LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['WHITELISTING_SKIPPED'], [ '{reason}' => 'segment was missing, hence','{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{variation}' => 'for variation:' . $variation['name']], self::$CLASSNAME);
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
    private static function evaluateBestVariation($validVariations, $totalVariationTraffic, $userId)
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

    /**
     * scale vartion of every varition used in case when
     * multiple vartions satisfy whitelisting condition
     * @param $variations
     * @param $totalVariationTraffic
     * @return mixed
     */
    private static function scaleVariations($variations, $totalVariationTraffic)
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
}
