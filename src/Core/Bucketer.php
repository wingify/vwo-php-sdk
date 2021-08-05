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

use Monolog\Logger;
use vwo\Constants\LogMessages;
use vwo\Services\LoggerService;
use vwo\Utils\Murmur as murmur;

/***
 * Class Bucketer
 * It helps in bucketing the user
 */
class Bucketer
{

    private static $SEED = 1;
    private static $MAX_VALUE = 0x100000000;
    private static $MAX_RANGE = 10000;
    private static $CLASSNAME = 'vwo\BucketService';


    public static $MAX_CAMPAIGN_TRAFFIC = 100;

    /***
     * get max limit
     *
     * @param  $weight
     * @return float
     */

    public static function getLimit($weight)
    {
        return floor($weight * self::$MAX_RANGE / 100);
    }

    /***
     * to fetch the variation id
     *
     * @param  $campaign      campaign array
     * @param  $variationName
     * @return array|null
     */
    public static function getBucketVariationId($campaign, $variationName)
    {
        foreach ($campaign['variations'] as $variation) {
            if ($variation['name'] == $variationName) {
                $variationInfo = ['name' => $variation['name'], 'id' => $variation['id']];
                if (isset($variation['isFeatureEnabled'])) {
                    $variationInfo['isFeatureEnabled'] = $variation['isFeatureEnabled'];
                }
                return $variationInfo;
            }
        }
        return null;
    }

    /***
     * To get the bucket value using userId and campaign
     *
     * @param  $userid
     * @param  $campaign
     * @param  bool $disableLogs optional: disable logs if True
     * @return array|null
     */
    public static function getBucket($userId, $campaign, $disableLogs = false)
    {
        // if bucketing to be done
        $bucketVal = self::getBucketVal($userId, self::$MAX_CAMPAIGN_TRAFFIC);
        if (!self::isUserPartofCampaign($bucketVal, $campaign['percentTraffic'])) {
            LoggerService::log(Logger::DEBUG, LogMessages::DEBUG_MESSAGES['USER_NOT_PART_OF_CAMPAIGN'], ['{userId}' => $userId, '{method}' => 'getBucket', '{campaignKey}' => $campaign['key']], self::$CLASSNAME, $disableLogs);
            return null;
        }
        $multiplier = self::getMultiplier($campaign['percentTraffic']);

        $rangeForVariations = self::getRangeForVariations($bucketVal, $multiplier);

        $variation = self::variationUsingRange($rangeForVariations, $campaign['variations']);
        LoggerService::log(Logger::DEBUG, LogMessages::DEBUG_MESSAGES['VARIATION_HASH_BUCKET_VALUE'], ['{userId}' => $userId,'{bucketValue}' => $rangeForVariations, '{percentTraffic}' => $campaign['percentTraffic'], '{campaignKey}' => $campaign['key']], self::$CLASSNAME);
        if ($variation !== null) {
            LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['GOT_VARIATION_FOR_USER'], ['{variationName}' => $variation['name'], '{userId}' => $userId, '{method}' => 'getBucket', '{campaignKey}' => $campaign['key']], self::$CLASSNAME);
            return $variation;
        }
        LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['NO_VARIATION_ALLOCATED'], ['{userId}' => $userId, '{campaignKey}' => $campaign['key']], self::$CLASSNAME);
        return null;
    }

    public static function variationUsingRange($rangeForVariations, $variations)
    {
        foreach ($variations as $variation) {
            if (isset($variation['max_range']) && $variation['max_range'] >= $rangeForVariations && $rangeForVariations >= $variation['min_range']) {
                return $variation;
            }
        }
        return null;
    }

    /***
     * Returns a campaign by checking the Start and End Bucket Allocations of each campaign.
     *
     * @param  $rangeForCampaigns int: the bucket value of the user
     * @param  $campaigns         array of campaigns
     * @return array|null
     */
    public static function getCampaignUsingRange($rangeForCampaigns, $campaigns)
    {
        $rangeForCampaigns = $rangeForCampaigns * self::$MAX_RANGE;
        foreach ($campaigns as $campaign) {
            if (isset($campaign['max_range']) && $campaign['max_range'] >= $rangeForCampaigns && $rangeForCampaigns >= $campaign['min_range']) {
                return $campaign;
            }
        }
        return null;
    }

    /***
     * return range of the current string value
     *
     * @param  $str
     * @param  $maxPercent
     * @return float|int
     */
    /*
    * The function getBucketVal has references from “Optimizely PHP SDK, version 3.2.0” Copyright 2016-2019, Optimizely,
    * Copyright 2016-2019, Optimizely, used under Apache 2.0 License.
    * Source - https://github.com/optimizely/php-sdk/blob/master/src/Optimizely/Bucketer.php
    */
    public static function getBucketVal($str)
    {
        $code = self::getmurmurHash_Int($str);
        $range = $code / self::$MAX_VALUE;
        if ($range < 0) {
            $range += (10000 / (self::$MAX_RANGE));
        }
        return $range;
    }

    /**
     * get murmurhash from the murmur file
     *
     * @param  $str
     * @return number
     */

    public static function getmurmurHash_Int($str)
    {
        return $hash = Murmur::hash3_int($str, self::$SEED);
    }

    /**
     * check if the bucketvalue is part of campaign or not on basis of percent traffic
     *
     * @param  $bucketVal
     * @param  $percentTraffic
     * @return bool
     */
    public static function isUserPartofCampaign($bucketVal, $percentTraffic)
    {
        if (floor($bucketVal * self::$MAX_CAMPAIGN_TRAFFIC) > $percentTraffic) {
            return false;
        }
        return true;
    }

    /**
     * to find out the value of multiplier
     *
     * @param  $traffic
     * @return float|int
     */
    public static function getMultiplier($traffic)
    {
        return self::$MAX_CAMPAIGN_TRAFFIC / ($traffic);
    }

    /**
     * fetch the range of the variation
     *
     * @param  $range
     * @param  $multiplier
     * @return int
     */
    public static function getRangeForVariations($range, $multiplier = 1)
    {
        return intval(floor(($range * self::$MAX_RANGE) + 1) * $multiplier);
    }

    /**
     * function to calculate range of every variation
     *
     * @param  $variations
     * @return array variation array
     */
    public static function addRangesToVariations($variations)
    {
        return self::addRanges($variations);
    }

    /**
     * To allocate range to every campaign
     *
     * @param  array $campaigns array of campaigns
     * @return array variation array
     */
    public static function addRangesToCampaigns($campaigns)
    {
        return self::addRanges($campaigns);
    }

    /**
     * To allocate ranges to every entities (campaigns/variations)
     *
     * @param  array $entities array of campaigns or variations
     * @return mixed
     */
    private static function addRanges($entities)
    {
        $offset = 0;
        foreach ($entities as $vkey => $entity) {
            $limit = Bucketer::getLimit($entity['weight']);
            $max_range = $offset + $limit;
            $entities[$vkey]['min_range'] = $offset + 1;
            $entities[$vkey]['max_range'] = $max_range;
            $offset = $max_range;
        }
        return $entities;
    }
}
