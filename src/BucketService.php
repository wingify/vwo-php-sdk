<?php
namespace vwo;
use Monolog\Logger;
use vwo\Utils\Constants;
use vwo\Utils\murmur as murmur;

/***
 * Class BucketService
 * It helps in bucketing the user
 * @package vwo
 * @author Gaurav Gupta
 * @version 1.0
 * @copyright never
 */

Class BucketService{

    private static $SEED = 1;
    private static $MAX_VALUE=0x100000000;
    private static $MAX_RANGE=10000;
    private static $MAX_CAMPAIGN_TRAFFIC=100;
    private static $CLASSNAME='vwo\BucketService';

    /***
     *
     * get murmurhash from the murmur file
     *
     * @param $str
     * @return number
     */

    public static function getmurmurHash_Int($str){
        return $hash=Murmur::hash3_int($str, self::$SEED);
    }

    /***
     *
     * return range of the current string value
     *
     * @param $str
     * @param $maxPercent
     * @return float|int
     */

    public static function getBucketVal($str,$maxPercent){
        $code=self::getmurmurHash_Int($str);

        $range = $code / self::$MAX_VALUE;

        if ($range < 0) {

            $range += (10000/(self::$MAX_RANGE));
        }
        return $range;
    }

    /****
     *
     * check if the bucketvalue is part of campaign or not on basis of percent traffic
     *
     * @param $bucketVal
     * @param $percentTraffic
     * @return bool
     */
    public static function isUserPartofCampaign($bucketVal,$percentTraffic){

        if( floor($bucketVal * self::$MAX_CAMPAIGN_TRAFFIC ) > $percentTraffic){
            return FALSE;
        }
        return TRUE;
    }

    /**
     *
     * fetch the range of the variation
     * @param $range
     * @param $multiplier
     * @return int
     */

    private static function getRangeForVariations($range,$multiplier){

        return  intval(floor(($range*self::$MAX_RANGE)+1)*$multiplier);
    }

    /***
     *
     * get max limit
     *
     * @param $weight
     * @return float
     */

    public static function getLimit($weight){
        return floor($weight * self::$MAX_RANGE/100);
    }

    /***
     *
     * to fetch the variation id
     *
     * @param $campaign
     * @param $variationName
     * @return array|null
     */

    public static function getBucketVariationId($campaign,$variationName){
        foreach ( $campaign['variations'] as $variation ) {
            if($variation['name']==$variationName){
                return ['name'=>$variation['name'],'id'=>$variation['id']];
            }
        }
        return null;

    }

    /**
     *
     * to find out the value of multiplier
     * @param $traffic
     * @return float|int
     */
    public static function getMultiplier($traffic){
        return self::$MAX_CAMPAIGN_TRAFFIC/($traffic);

    }

    /***
     *
     * To get the bucket value using userId and campaign
     *
     * @param $userid
     * @param $campaign
     * @return array|null
     */
    public static function getBucket($userId,$campaign){

        // if bucketing to be done
        $bucketVal= self::getBucketVal($userId,self::$MAX_CAMPAIGN_TRAFFIC);
        if(!self::isUserPartofCampaign($bucketVal,$campaign['percentTraffic'])){
            VWO::addLog(Logger::DEBUG,Constants::DEBUG_MESSAGES['USER_NOT_PART_OF_CAMPAIGN'],['{userId}'=>$userId,'{method}'=>'getBucket','{campaignTestKey}'=>$campaign['key']],self::$CLASSNAME);
            return null;
        }
        $multiplier=self::getMultiplier($campaign['percentTraffic']);
        $rangeForVariations=self::getRangeForVariations($bucketVal,$multiplier);
        foreach ( $campaign['variations'] as $variation ) {
            if($variation['max_range']>=$rangeForVariations && $rangeForVariations>=$variation['min_range']){
                VWO::addLog(Logger::INFO,Constants::INFO_MESSAGES['GOT_VARIATION_FOR_USER'],['{variationName}'=>$variation['name'],'{userId}'=>$userId,'{method}'=>'getBucket','{campaignTestKey}'=>$campaign['key']],self::$CLASSNAME);
                return $variatInfo=['name'=>$variation['name'],'id'=>$variation['id']];
            }
        }
        VWO::$_logger->addLog(Logger::INFO,Constants::INFO_MESSAGES['NO_VARIATION_ALLOCATED'],['{userId}'=>$userId,'{campaignTestKey}'=>$campaign['key']],self::$CLASSNAME);
        return null;
    }
}