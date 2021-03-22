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

use vwo\VWO;
use Monolog\Logger;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator as Validator;
use JsonSchema\Constraints\Constraint;
use vwo\Utils\COmmon as CommonUtil;
use vwo\Utils\SegmentEvaluator as SegmentEvaluator;
use vwo\Services\LoggerService;
use vwo\Constants\LogMessages as LogMessages;

/***
 * Class Validations
 * All the validations will be done using Class Validations
 *
 * @package vwo\Utils
 */
class Validations
{

    static $CLASSNAME = 'vwo\Utils\Validations';

    /**
     * schema for settings array
     *
     * @var array
     */
    static $jsonSchemaObject = [
        "type" => "array",
        "properties" => [
            "sdkKey" => ["type" => "string"],
            "version" => ["type" => "number"],
            "accountId" => ["type" => "number"],
            "campaigns" => [
                'type' => 'array',
                "goals" => ["type" => "array",
                    "identifier" => ["type" => "string"],
                    "type" => ["type" => "string"],
                    "id" => ["type" => "number"],
                ],
                "variations" => [
                    "type" => "array",
                    "name" => ["type" => "string"],
                    "weight" => ["type" => "number"],
                    "id" => ["type" => "number"],],
                "percentTraffic" => ["type" => "number"],
                "key" => ["type" => "string"],
                "status" => ["type" => "string"],
            ],
        ],


    ];

    /**
     * Validate the tags and userId for push api
     *
     * @param  string $tagKey
     * @param  string $tagValue
     * @param  string $userId
     * @return bool
     */
    public static function pushApiParams($tagKey, $tagValue, $userId)
    {
        if (!is_string($userId)  || empty($userId)) {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['INVALID_USER_ID'], ['{userId}' => $userId,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (!is_string($tagKey) || empty($tagKey)) {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['TAG_KEY_CORRUPTED'], ['{tagKey}' => $tagKey,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (strlen($tagKey) > 255) {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['TAG_KEY_LENGTH_ERROR'], ['{tagKey}' => $tagKey,'{userId}' => $userId,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (!is_string($tagValue)  || empty($tagValue)) {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['TAG_VALUE_CORRUPTED'], ['{tagValue}' => $tagValue,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (strlen($tagValue) > 255) {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['TAG_VALUE_LENGTH_ERROR'], ['{tagValue}' => $tagValue,'{userId}' => $userId,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }

        return true;
    }

    /**
     * this function check whether pre-segmentation is passed or not
     * @param array $campaign
     * @param string $userId
     * @param array $options
     * @return bool
     */
    public static function checkPreSegmentation($campaign, $userId, $options)
    {
        $customVariables = CommonUtil::getValueFromOptions($options, 'customVariables');
        $segment = new SegmentEvaluator();
        if (array_key_exists('segments', $campaign) && count($campaign['segments'])) {
            $response = $segment->evaluate($campaign['segments'], $customVariables);
             LoggerService::log(
                 Logger::INFO,
                 LogMessages::INFO_MESSAGES['SEGMENTATION_STATUS'],
                 [
                             '{status}' => $response === true ? 'passed' : 'failed',
                             '{campaignKey}' => $campaign['key'],
                             '{userId}' => $userId,
                             '{customVariables}' => json_encode($customVariables),
                             '{segmentationType}' => 'pre-segmentation',
                             '{variation}' => ''
                         ],
                 self::$CLASSNAME
             );
            return $response;
        } else {
            LoggerService::log(Logger::INFO, LogMessages::INFO_MESSAGES['SEGMENTATION_SKIPPED'], ['{campaignKey}' => $campaign['key'],'{userId}' => $userId,'{variation}' => '']);
            return true;
        }
    }

    /**
     * function to validation request setting schema provided by client
     *
     * @param  $request
     * @return bool
     */
    public static function checkSettingSchema($request)
    {
        $response = false;
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://mySchema', self::$jsonSchemaObject);
        $jsonValidator = new Validator(new Factory($schemaStorage));
        $jsonValidator->validate($request, self::$jsonSchemaObject, Constraint::CHECK_MODE_VALIDATE_SCHEMA);
        if ($jsonValidator->isValid()) {
            $response = true;
            LoggerService::log(Logger::DEBUG, LogMessages::DEBUG_MESSAGES['VALID_CONFIGURATION']);
        } else {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['SETTINGS_FILE_CORRUPTED']);
        }
        return $response;
    }

    /**
     * campaignkey and userid validation for feature enable
     * @param string $campaignKey
     * @param string $userId
     * @return bool
     */
    public static function validateIsFeatureEnabledParams($campaignKey, $userId)
    {
        if (self::validateCampaignKey($campaignKey) && self::validateUserId($userId)) {
            return true;
        }
        return false;
    }

    /**
     * check whether campaignKey provided by end user is present in settings on VWO or not
     * @param $campaignKey string
     * @return bool
     */
    public static function validateCampaignKey($campaignKey)
    {
        if (is_string($campaignKey)) {
            return true;
        }
        LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['FEATURE_KEY_CORRUPTED'], ['{campaignKey}' => $campaignKey], self::$CLASSNAME);
        return false;
    }

    /**
     * user id should be string type
     * @param string $userId
     * @return bool
     */
    public static function validateUserId($userId)
    {
        if (is_string($userId)) {
            return true;
        }
        LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['USERID_KEY_CORRUPTED'], ['{userId}' => $userId], self::$CLASSNAME);
        return false;
    }

    /**
     * function to check if the campaignkey exists in campaign array from settings
     *
     * @param  string $campaignKey
     * @param  array $settings
     * @return null
     */
    public static function getCampaignFromCampaignKey($campaignKey, $settings)
    {
        if (isset($settings) && isset($settings['campaigns'])) {
            foreach ($settings['campaigns'] as $campaign) {
                if (isset($campaign['status']) && $campaign['status'] !== 'RUNNING') {
                    continue;
                }
                if ($campaignKey === $campaign['key']) {
                    return $campaign;
                }
            }
        }
        LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['CAMPAIGN_NOT_RUNNING'], ['{campaignKey}' => $campaignKey], self::$CLASSNAME);
        return null;
    }

    /**
     * fetch all running campaigns (with campaignKey in $campaignKeys array) from settings
     *
     * @param  array $campaignKeys
     * @param  array $settings
     * @param  string $goalIdentifier
     * @param  string $goalTypeToTrack
     * @return array
     */
    private static function getCampaignsFromCampaignKeys($campaignKeys, $settings, $goalIdentifier, $goalTypeToTrack = 'ALL')
    {
        $campaigns = [];
        foreach ($campaignKeys as $campaignKey) {
            $campaign = self::getCampaignForCampaignKeyAndGoal($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack);
            if ($campaign) {
                $campaigns[] = $campaign;
            }
        }
        return $campaigns;
    }

    /**
     * fetch all running campaigns (having goal identifier $goalIdentifier and goal type CUSTOM|REVENUE|ALL) from settings
     *
     * @param  array $settings
     * @param  string $goalIdentifier
     * @param  string $goalTypeToTrack
     * @return array
     */
    private static function getCampaignsForGoal($settings, $goalIdentifier, $goalTypeToTrack = 'ALL')
    {
        $campaigns = [];
        if (isset($settings) && isset($settings['campaigns'])) {
            foreach ($settings['campaigns'] as $campaign) {
                if (isset($campaign['status']) && $campaign['status'] !== 'RUNNING') {
                    continue;
                }
                $goal = CommonUtil::getGoalFromGoals($campaign['goals'], $goalIdentifier);
                if (self::validateGoal($goal, $goalTypeToTrack)) {
                    $campaigns[] = $campaign;
                }
            }
        }
        return $campaigns;
    }

    /**
     * fetch campaigns from settings
     *
     * @param  string|array|null $campaignKey
     * @param  array $settings
     * @param  string $goalIdentifier
     * @param  string $goalTypeToTrack
     * @return array
     */
    public static function getCampaigns($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack = 'ALL')
    {
        $campaigns = [];
        if (!$campaignKey) {
            $campaigns = self::getCampaignsForGoal($settings, $goalIdentifier, $goalTypeToTrack);
        } elseif (is_array($campaignKey)) {
            $campaigns = self::getCampaignsFromCampaignKeys($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack);
        } elseif (is_string($campaignKey)) {
            $campaign = self::getCampaignForCampaignKeyAndGoal($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack);
            if ($campaign) {
                $campaigns[] = $campaign;
            }
        }
        if (count($campaigns) == 0) {
            LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['NO_CAMPAIGN_FOUND'], ['{goalIdentifier}' => $goalIdentifier], self::$CLASSNAME);
        }
        return $campaigns;
    }

    /**
     * fetch a campaign for given campaignKey (having goal identifier $goalIdentifier and goal type CUSTOM|REVENUE|ALL) from settings
     *
     * @param  string|array $campaignKey
     * @param  array $settings
     * @param  string $goalIdentifier
     * @param  string $goalTypeToTrack
     * @return array|null
     */
    public static function getCampaignForCampaignKeyAndGoal($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack)
    {
        $campaign = self::getCampaignFromCampaignKey($campaignKey, $settings);
        if ($campaign) {
            $goal = CommonUtil::getGoalFromGoals($campaign['goals'], $goalIdentifier);
            if (self::validateGoal($goal, $goalTypeToTrack)) {
                return $campaign;
            }
        }
        return null;
    }

    public static function validateGoal($goal, $goalTypeToTrack)
    {
        return (
            $goal &&
            (
                $goalTypeToTrack === 'ALL' ||
                (
                    in_array($goal['type'], VWO::GOAL_TYPES) &&
                    array_key_exists($goalTypeToTrack, VWO::GOAL_TYPES) &&
                    $goal['type'] == VWO::GOAL_TYPES[$goalTypeToTrack]
                )
            )
        );
    }
}
