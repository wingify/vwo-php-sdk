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

use vwo\Constants\FileNameEnum;
use vwo\Constants\Urls;

/***
 * All the common function will be invoked from common  class
 *
 * Class Common
 *
 * @package vwo\Utils
 */
class Common
{
    private static $CLASSNAME = FileNameEnum::COMMON;

    /***
     * basic creation of log message from constants.php
     *
     * @param  $message
     * @param  $params
     * @param  $className
     * @return mixed
     */
    public static function buildLogMessage($message, $params, $className = '', $apiName = '')
    {
        $params['{file}'] = $className;
        $response = str_replace(array_keys($params), array_values($params), $message);
        return '[' . $apiName . '] ' . $response;
    }

    /**
     * method to create the input array for user-storage set function
     *
     * @param  string $campaignKey
     * @param  array  $variation
     * @param  string $userId
     * @param  string $goalIdentifier
     * @return array
     */
    public static function getUserCampaignVariationMapping($campaignKey, $variation, $userId, $goalIdentifier)
    {
        $data =  [
            'userId' => $userId,
            'variationName' => $variation['name'],
            'campaignKey' => $campaignKey,
        ];
        if ($goalIdentifier) {
            $data['goalIdentifier'] = $goalIdentifier;
        }
        return $data;
    }


    /**
     * To fetch the goal id using goals array and goal identifier
     *
     * @param  $goals
     * @param  $goalIdentifier
     * @return int
     */
    public static function getGoalFromGoals($goals, $goalIdentifier)
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
    public static function getVariableValue($variables, $variableKey)
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
        case 'json':
            return $value;
        }
    }

    /**
     * this is used to fetch the main variation of the campaign
     * mostly used where default values need to assigned
     *
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

    public static function getRandomNumber()
    {
        return (time() / 10);
    }

    public static function getCurrentUnixTimestampInMillis()
    {
        return round(microtime(true) * 1000);
    }

    /**
     * get URL
     *
     * @param  string $endPoint
     * @return string
     */
    public static function getUrl($endPoint)
    {
        return DataLocationManager::instance()->getDataLocation() . Urls::SERVER_SIDE_URI . $endPoint;
    }

    /**
     * get events URL
     *
     * @return string
     */
    public static function getEventsUrl()
    {
        return DataLocationManager::instance()->getDataLocation() . Urls::EVENTS_ENDPOINT;
    }

    /**
     * get batch events URL
     *
     * @return string
     */
    public static function getBatchEventsUrl()
    {
        return DataLocationManager::instance()->getDataLocation() . Urls::SERVER_SIDE_URI . Urls::BATCH_EVENTS_ENDPOINT;
    }
}
