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

use Monolog\Logger;
use vwo\Constants\CampaignTypes;
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
     *
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
     *
     * @param  $campaign
     * @param  $userId
     * @param  $options
     * @param  bool $disableLogs disable logs if True
     * @return array|null
     */
    public static function findVariationFromWhiteListing($campaign, $userId, $options, $disableLogs = false)
    {
        $bucketInfo = null;
        if (isset($campaign['isForcedVariationEnabled']) && $campaign['isForcedVariationEnabled'] == true) {
            $variationTargetingVariables = CommonUtil::getValueFromOptions($options, 'variationTargetingVariables');
            $bucketInfo = self::getForcedBucket($campaign, $userId, $variationTargetingVariables, $disableLogs);
            $status = $bucketInfo != null ? 'satisfy' : "didn't satisfy";

            LoggerService::log(
                Logger::DEBUG,
                LogMessages::INFO_MESSAGES['WHITELISTING_ELIGIBILITY_STATUS'],
                [
                    '{status}' => $status,
                    '{userId}' => $userId,
                    '{variation}' => $status == 'satisfy' ? ($campaign["type"] == CampaignTypes::FEATURE_ROLLOUT ? 'and hence becomes part of the rollout' : $bucketInfo['name'] . 'and hence variation is assigned') : '',
                    '{campaign_key}' => $campaign['key'],
                    '{variation_targeting_variables}' => json_encode($variationTargetingVariables)
                ],
                self::$CLASSNAME,
                $disableLogs
            );
        } else {
            LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['WHITELISTING_SKIPPED'], [ '{reason}' => '','{userId}' => $userId, '{campaignKey}' => $campaign['key'],'{variation}' => ''], self::$CLASSNAME, $disableLogs);
        }
        return $bucketInfo;
    }

    /**
     * this function will evaluate the bucket out of
     * the campaign for whitelisted variation
     *
     * @param  $campaign
     * @param  $userId
     * @param  $variationTargetingVariables
     * @param  bool $disableLogs                 disable logs if True
     * @return array|null
     */
    private static function getForcedBucket($campaign, $userId, $variationTargetingVariables, $disableLogs = false)
    {
        if(isset($campaign["isUserListEnabled"]) && $campaign["isUserListEnabled"]) {
            $variationTargetingVariables['_vwoUserId'] = UuidUtil::get($userId, AccountUtil::instance()->getAccountId());
        } else {
            $variationTargetingVariables['_vwoUserId'] = $userId;
        }
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
                LoggerService::log(
                    Logger::INFO,
                    LogMessages::INFO_MESSAGES['SEGMENTATION_STATUS'],
                    [
                        '{userId}' => $userId,
                        '{campaignKey}' => $campaign['key'],
                        '{segmentationType}' => 'whitelisting',
                        '{variation}' => $campaign['type'] == CampaignTypes::FEATURE_ROLLOUT ? '' : 'for variation:' . $variation['name'],
                        '{status}' => $result === true ? 'passed' : 'failed',
                        '{customVariables}' => json_encode($variationTargetingVariables)
                    ],
                    self::$CLASSNAME,
                    $disableLogs
                );
            } else {
                LoggerService::log(
                    Logger::INFO,
                    LogMessages::INFO_MESSAGES['WHITELISTING_SKIPPED'],
                    [
                        '{reason}' => 'segment was missing, hence',
                        '{userId}' => $userId,
                        '{campaignKey}' => $campaign['key'],
                        '{variation}' => $campaign['type'] == CampaignTypes::FEATURE_ROLLOUT ? '' : 'for variation:' . $variation['name']
                    ],
                    self::$CLASSNAME,
                    $disableLogs
                );
            }
        }
        $totalValidVariations = count($validVariations);
        if ($totalValidVariations == 1) {
            return $validVariations[0];
        } elseif ($totalValidVariations > 1) {
            return self::evaluateBestVariation($validVariations, $totalVariationTraffic, $userId, $campaign);
        }
        return null;
    }

    /**
     * @param  $validVariations
     * @param  $totalVariationTraffic
     * @param  $userId
     * @param  $campaign
     * @return null| array of variation
     */
    private static function evaluateBestVariation($validVariations, $totalVariationTraffic, $userId, $campaign)
    {
        //scale and assign ranges to the variations
        $validVariations = self::scaleVariations($validVariations, $totalVariationTraffic);
        $validVariations = Bucketer::addRangesToVariations($validVariations);
        //find murmur
        $bucketVal = Bucketer::getBucketVal($userId, $campaign);
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
     *
     * @param  $variations
     * @param  $totalVariationTraffic
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

    /**
     * It extracts the weights from all the campaigns and scales them so that
     * the total sum of eligible campaigns' weights become 100%.
     *
     * @param  array $campaigns campaigns part of group which were eligible to be winner
     * @return array
     */
    public static function scaleCampaigns($campaigns)
    {
        $normalizedWeight = 100 / count($campaigns);
        foreach ($campaigns as $index => $campaign) {
            $campaigns[$index]["weight"] = $normalizedWeight;
        }
        return $campaigns;
    }

    /**
     * Checks whether a campaign is part of a group.
     *
     * @param  array $settings   Settings file for the project
     * @param  int   $campaignId Id of campaign which is to be checked
     * @return bool
     */
    public static function isPartOfGroup($settings, $campaignId)
    {
        if (isset($settings["campaignGroups"]) && $settings["campaignGroups"] && array_key_exists($campaignId, $settings["campaignGroups"])) {
            return true;
        }
        return false;
    }

    /**
     * Returns campaigns which are part of given group using group_id.
     *
     * @param  array $settings Settings file for the project
     * @param  int   $groupId  id of group whose campaigns are to be return
     * @return array
     */
    public static function getGroupCampaigns($settings, $groupId)
    {
        $groupCampaignIds = [];
        $groupCampaigns = [];
        $groups = $settings["groups"];

        if($groups && array_key_exists($groupId, $groups)) {
            $groupCampaignIds = $groups[$groupId]["campaigns"];
        }

        if($groupCampaignIds) {
            foreach ($groupCampaignIds as $campaignId) {
                foreach ($settings["campaigns"] as $campaign) {
                    if($campaign["id"] == $campaignId && $campaign["status"] == 'RUNNING') {
                        $groupCampaigns[] = $campaign;
                    }
                }
            }
        }
        return $groupCampaigns;
    }
}
