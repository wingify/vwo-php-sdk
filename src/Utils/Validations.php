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
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator as Validator;
use JsonSchema\Constraints\Constraint;
use Exception as Exception;
use vwo\Constants\CampaignTypes;
use vwo\Constants\FileNameEnum;
use vwo\Storage\UserStorageInterface;
use vwo\VWO;
use vwo\Utils\Common as CommonUtil;
use vwo\Utils\SegmentEvaluator as SegmentEvaluator;
use vwo\Services\LoggerService;

/***
 * Class Validations
 * All the validations will be done using Class Validations
 *
 * @package vwo\Utils
 */
class Validations
{
    const CLASSNAME = FileNameEnum::VALIDATIONS;

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
            "isEventArchEnabled" => ["type" => "boolean"],
            "dataResidencyLocation" => ["type" => "string"],
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
                'name' => ["type" => "string"],
                "isUserListEnabled" => ["type" => "boolean"]
            ],
        ],


    ];

    /**
     * Validate the tags and userId for push api
     *
     * @param  string $userId
     * @param  array  $customDimensionMap
     * @return bool
     */
    public static function pushApiParams($userId, $customDimensionMap = [])
    {
        if (!is_string($userId)  || empty($userId)) {
            return false;
        }

        if (empty($customDimensionMap) || !is_array($customDimensionMap)) {
            return false;
        }

        foreach ($customDimensionMap as $tagKey => $tagValue) {
            if (!is_string($tagKey) || empty($tagKey)) {
                return false;
            }
            if (strlen($tagKey) > 255) {
                LoggerService::log(Logger::ERROR, 'TAG_KEY_LENGTH_EXCEEDED', ['{tagKey}' => $tagKey,'{userId}' => $userId], self::CLASSNAME);
                return false;
            }
            if (!is_string($tagValue)  || empty($tagValue)) {
                return false;
            }
            if (strlen($tagValue) > 255) {
                LoggerService::log(Logger::ERROR, 'TAG_VALUE_LENGTH_EXCEEDED', ['{tagValue}' => $tagValue,'{userId}' => $userId], self::CLASSNAME);
                return false;
            }
        }

        return true;
    }

    /**
     * this function check whether pre-segmentation is passed or not
     *
     * @param  array  $campaign
     * @param  string $userId
     * @param  array  $options
     * @param  bool   $disableLogs optional: disable logs if True
     * @return bool
     */
    public static function checkPreSegmentation($campaign, $userId, $options, $disableLogs = false)
    {
        $customVariables = CommonUtil::getValueFromOptions($options, 'customVariables');
        $segment = new SegmentEvaluator();
        try {
            if (array_key_exists('segments', $campaign) && count($campaign['segments'])) {
                $response = $segment->evaluate($campaign['segments'], $customVariables);
                LoggerService::log(
                    Logger::INFO,
                    'SEGMENTATION_STATUS',
                    [
                        '{status}' => $response === true ? 'passed' : 'failed',
                        '{campaignKey}' => $campaign['key'],
                        '{userId}' => $userId,
                        '{customVariables}' => json_encode($customVariables),
                        '{segmentationType}' => 'pre-segmentation',
                        '{variation}' => ''
                    ],
                    self::CLASSNAME,
                    $disableLogs
                );
                return $response;
            } else {
                LoggerService::log(
                    Logger::DEBUG,
                    'SEGMENTATION_SKIPPED',
                    [
                        '{campaignKey}' => $campaign['key'],
                        '{variation}' => '',
                        '{userId}' => $userId
                    ],
                    self::CLASSNAME,
                    $disableLogs
                );
                return true;
            }
        } catch (Exception $e) {
            LoggerService::log(
                Logger::ERROR,
                'SEGMENTATION_ERROR',
                [
                    '{campaignKey}' => $campaign['key'],
                    '{variation}' => '',
                    '{userId}' => $userId,
                    '{customVariables}' => json_encode($customVariables),
                    '{err}' => $e->getMessage()
                ],
                self::CLASSNAME,
                $disableLogs
            );
            return false;
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
        if ($jsonValidator->isValid() && self::validateVariablesInCampaigns($request["campaigns"])) {
            $response = true;
            LoggerService::log(Logger::INFO, 'CONFIG_VALID', [], self::CLASSNAME);
        } else {
            LoggerService::log(Logger::ERROR, 'SETTINGS_FILE_CORRUPTED', [], self::CLASSNAME);
        }
        return $response;
    }

    /**
     * campaignKey and userid validation for feature enable
     *
     * @param  string $campaignKey
     * @param  string $userId
     * @param  string $apiName
     * @return bool
     */
    public static function validateIsFeatureEnabledParams($campaignKey, $userId, $apiName = '')
    {
        if (self::validateCampaignKey($campaignKey, $apiName) && self::validateUserId($userId)) {
            return true;
        }
        return false;
    }

    /**
     * validate variables present in settings file
     *
     * @param  $campaigns array
     * @return bool
     */
    private static function validateVariablesInCampaigns($campaigns)
    {
        foreach ($campaigns as $campaign) {
            if ($campaign["type"] == CampaignTypes::FEATURE_TEST) {
                foreach ($campaign["variations"] as $variation) {
                    if (!self::validateVariables($variation["variables"])) {
                        return false;
                    }
                }
            } elseif ($campaign["type"] == CampaignTypes::FEATURE_ROLLOUT) {
                if (!self::validateVariables($campaign["variables"])) {
                    return false;
                }
            }
        }
        return true;
    }

    private static function validateVariables($variables)
    {
        if (isset($variables) && is_array($variables)) {
            foreach ($variables as $variable) {
                if (isset($variable["key"]) && isset($variable["id"]) && isset($variable["type"]) && isset($variable["value"])) {
                    // validate variable key & type are string and id is integer
                    if (!is_string($variable["key"]) || !is_int($variable["id"]) || !is_string($variable["type"])) {
                        return false;
                    }

                    // validate variable type and type of variable value is same
                    if ($variable["type"] != 'json' && $variable["type"] != gettype($variable["value"])) {
                        return false;
                    } elseif ($variable["type"] == 'json') {
                        if (!is_array($variable["value"])) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * check whether campaignKey provided by end user is present in settings on VWO or not
     *
     * @param  $campaignKey string
     * @param  $apiName     string
     * @return bool
     */
    public static function validateCampaignKey($campaignKey, $apiName)
    {
        if (is_string($campaignKey)) {
            return true;
        }
        LoggerService::log(Logger::ERROR, 'CONFIG_PARAMETER_INVALID', ['{parameter}' => 'campaignKey', '{api}' => $apiName, '{type}' => 'string'], self::CLASSNAME);
        return false;
    }

    /**
     * user id should be string type
     *
     * @param  string $userId
     * @return bool
     */
    public static function validateUserId($userId)
    {
        if (is_string($userId)) {
            return true;
        }
        LoggerService::log(Logger::ERROR, 'USER_ID_INVALID', ['{userId}' => $userId], self::CLASSNAME);
        return false;
    }

    /**
     * function to check if the campaignkey exists in campaign array from settings
     *
     * @param  string $campaignKey
     * @param  array  $settings
     * @param  string $apiName
     * @return null
     */
    public static function getCampaignFromCampaignKey($campaignKey, $settings, $apiName)
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
        LoggerService::log(Logger::WARNING, 'CAMPAIGN_NOT_RUNNING', ['{campaignKey}' => $campaignKey, '{api}' => $apiName], self::CLASSNAME);
        return null;
    }

    /**
     * fetch all running campaigns (with campaignKey in $campaignKeys array) from settings
     *
     * @param  array  $campaignKeys
     * @param  array  $settings
     * @param  string $goalIdentifier
     * @param  string $goalTypeToTrack
     * @param  string $apiName
     * @return array
     */
    private static function getCampaignsFromCampaignKeys($campaignKeys, $settings, $goalIdentifier, $goalTypeToTrack, $apiName)
    {
        $campaigns = [];
        foreach ($campaignKeys as $campaignKey) {
            $campaign = self::getCampaignForCampaignKeyAndGoal($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack, $apiName);
            if ($campaign) {
                $campaigns[] = $campaign;
            }
        }
        return $campaigns;
    }

    /**
     * fetch all running campaigns (having goal identifier $goalIdentifier and goal type CUSTOM|REVENUE|ALL) from settings
     *
     * @param  array  $settings
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
     * @param  array             $settings
     * @param  string            $goalIdentifier
     * @param  string            $goalTypeToTrack
     * @param  string            $apiName
     * @return array
     */
    public static function getCampaigns($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack, $apiName)
    {
        $campaigns = [];
        if (!$campaignKey) {
            $campaigns = self::getCampaignsForGoal($settings, $goalIdentifier, $goalTypeToTrack);
        } elseif (is_array($campaignKey)) {
            $campaigns = self::getCampaignsFromCampaignKeys($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack, $apiName);
        } elseif (is_string($campaignKey)) {
            $campaign = self::getCampaignForCampaignKeyAndGoal($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack, $apiName);
            if ($campaign) {
                $campaigns[] = $campaign;
            }
        }
        if (count($campaigns) == 0) {
            LoggerService::log(Logger::ERROR, 'CAMPAIGN_NOT_FOUND_FOR_GOAL', ['{goalIdentifier}' => $goalIdentifier], self::CLASSNAME);
        }
        return $campaigns;
    }

    /**
     * fetch a campaign for given campaignKey (having goal identifier $goalIdentifier and goal type CUSTOM|REVENUE|ALL) from settings
     *
     * @param  string|array $campaignKey
     * @param  array        $settings
     * @param  string       $goalIdentifier
     * @param  string       $goalTypeToTrack
     * @param  string       $apiName
     * @return array|null
     */
    public static function getCampaignForCampaignKeyAndGoal($campaignKey, $settings, $goalIdentifier, $goalTypeToTrack, $apiName)
    {
        $campaign = self::getCampaignFromCampaignKey($campaignKey, $settings, $apiName);
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

    /**
     * validate the sdk configuration passed during instantiation
     *
     * @param  array  $config
     * @param  string $apiName
     * @return bool
     */
    public static function validateSDKConfiguration($config, $apiName)
    {
        if (isset($config['isDevelopmentMode'])) {
            if (is_bool($config['isDevelopmentMode']) || in_array($config['isDevelopmentMode'], [0,1])) {
                self::validConfigLog('isDevelopmentMode', 'boolean');
            } else {
                self::invalidConfigLog('isDevelopmentMode', 'boolean', $apiName);
                return false;
            }
        }

        if (isset($config['userStorageService'])) {
            if ($config['userStorageService'] instanceof UserStorageInterface) {
                self::validConfigLog('userStorageService', 'object');
            } else {
                self::invalidConfigLog('userStorageService', 'object', $apiName);
                return false;
            }
        }

        return true;
    }

    /**
     * log information about valid parameter in sdk configuration passed during instantiation
     *
     * @param string $parameter
     * @param string $type
     */
    private static function validConfigLog($parameter, $type)
    {
        LoggerService::log(
            Logger::INFO,
            'CONFIG_PARAMETER_USED',
            [
                '{parameter}' => $parameter,
                '{type}' => $type
            ],
            self::CLASSNAME
        );
    }

    /**
     * log error about invalid parameter in sdk configuration passed during instantiation
     *
     * @param string $parameter
     * @param string $type
     */
    private static function invalidConfigLog($parameter, $type, $apiName)
    {
        LoggerService::log(
            Logger::ERROR,
            'CONFIG_PARAMETER_INVALID',
            [
                '{parameter}' => $parameter,
                '{type}' => $type,
                '{api}' => $apiName
            ],
            self::CLASSNAME
        );
    }
}
