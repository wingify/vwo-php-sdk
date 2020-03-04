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

use JsonSchema\SchemaStorage;
use JsonSchema\Validator as Validator;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;
use Monolog\Logger;
use vwo\Utils\SegmentEvaluator as SegmentEvaluator;
use vwo\VWO;
use vwo\Constants\Constants as Constants;

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
     * @param  $tagKey
     * @param  $tagValue
     * @param  $userId
     * @return bool
     */
    public static function pushApiParams($tagKey, $tagValue, $userId)
    {
        if (!is_string($userId)  || empty($userId)) {
            VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['INVALID_USER_ID'], ['{userId}' => $userId,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (!is_string($tagKey) || empty($tagKey)) {
            VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['TAG_KEY_CORRUPTED'], ['{tagKey}' => $tagKey,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (strlen($tagKey) > 255) {
            VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['TAG_KEY_LENGTH_ERROR'], ['{tagKey}' => $tagKey,'{userId}' => $userId,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (!is_string($tagValue)  || empty($tagValue)) {
            VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['TAG_VALUE_CORRUPTED'], ['{tagValue}' => $tagValue,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }
        if (strlen($tagValue) > 255) {
            VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['TAG_VALUE_LENGTH_ERROR'], ['{tagValue}' => $tagValue,'{userId}' => $userId,'{method}' => 'pushApiParams'], self::$CLASSNAME);
            return false;
        }

        return true;
    }

    public static function checkPreSegmentation($campaign, $userId, $options)
    {
        $customVariables = Common::getValueFromOptions($options, 'customVariables');
        $segment = new SegmentEvaluator();
        if (array_key_exists('segments', $campaign) && count($campaign['segments'])) {
            $response = $segment->evaluate($campaign['segments'], $customVariables);
             VWO::addLog(
                 Logger::INFO,
                 Constants::INFO_MESSAGES['SEGMENTATION_STATUS'],
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
            VWO::addLog(Logger::INFO, Constants::INFO_MESSAGES['SEGMENTATION_SKIPPED'], ['{campaignKey}' => $campaign['key'],'{userId}' => $userId,'{variation}' => '']);
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
            VWO::addLog(Logger::DEBUG, Constants::DEBUG_MESSAGES['VALID_CONFIGURATION']);
        } else {
            VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['SETTINGS_FILE_CORRUPTED']);
        }
        return $response;
    }

    public static function validateIsFeatureEnabledParams($campaignKey, $userId)
    {
        if (self::validateCampaignKey($campaignKey) && self::validateUserId($userId)) {
            return true;
        }
        return false;
    }

    public static function validateCampaignKey($campaignKey)
    {
        if (is_string($campaignKey)) {
            return true;
        }
        VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['FEATURE_KEY_CORRUPTED'], ['{campaignKey}' => $campaignKey], self::$CLASSNAME);
        return false;
    }

    public static function validateUserId($userId)
    {
        if (is_string($userId)) {
            return true;
        }
        VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['USERID_KEY_CORRUPTED'], ['{userId}' => $userId], self::$CLASSNAME);
        return false;
    }

    /**
     * function to check if the campaignkey exists in campign array from settings
     *
     * @param  $campaignKey
     * @return null
     */
    public static function getCampaignFromCampaignKey($campaignKey, $settings)
    {
        if (isset($settings['campaigns']) and count($settings['campaigns'])) {
            foreach ($settings['campaigns'] as $campaign) {
                if (isset($campaign['status']) && $campaign['status'] !== 'RUNNING') {
                    continue;
                }
                if ($campaignKey === $campaign['key']) {
                    return $campaign;
                }
            }
        }
        VWO::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['CAMPAIGN_NOT_RUNNING'], ['{campaignTestKey}' => $campaignKey], self::$CLASSNAME);
        return null;
    }
}
