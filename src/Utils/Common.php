<?php

/**
 * Copyright 2019 Wingify Software Pvt. Ltd.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace vwo\Utils;

use Monolog\Logger;
use vwo\VWO as VWO;
use vwo\Constants\Constants as Constants;
use Ramsey\Uuid\Uuid;

/***
 *
 * All the common function will be invoked from common  class
 *
 * Class Common
 * @package vwo\Utils
 */
class Common
{

    private static $CLASSNAME = 'vwo\Utils\Common';

    /***
     *
     * basic creation of log message from constants.php
     *
     * @param $message
     * @param $params
     * @param $className
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

    public static function getUserSaveData($campaignKey, $userData, $customerHash)
    {
        return [
            'userId' => $customerHash,
            'variationName' => $userData['name'],
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
     * @param $variables
     * @param $variableKey
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
     * @param $value
     * @param $type
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

    /**
     * @param $variations
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
     * @param $accountid
     * @param $userId
     * @param array $params
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
     * @param $userId
     * @param $accountId
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
     * @param $obj
     * @return array
     */
    public static function getKeyValue($obj)
    {
        $key = key($obj);
        $value = isset($obj[$key]) ? $obj[$key] : null;
        return [$key, $value];
    }
}
