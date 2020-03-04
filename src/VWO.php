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

namespace vwo;

use \Exception as Exception;
use vwo\Logger\LoggerInterface;
use vwo\Handlers\Connection as Connection;
use vwo\Storage\UserStorageInterface;
use vwo\Utils\Campaign;
use vwo\Utils\Validations as Validations;
use vwo\Constants\Constants as Constants;
use vwo\Utils\Common as Common;
use vwo\Logger\VWOLogger as VWOLogger;
use vwo\Core\Bucketer as Bucketer;
use Monolog\Logger as Logger;

/***
 * VWO sdk class
 *
 * Class VWO
 *
 * It helps in making client object and use the sdk
 */
class VWO
{
    /**
     * @var mixed|null|LoggerInterface
     * to save loggerinterface object
     */
    static $_logger;
    /****
     * @var static variables for log levels
     */

    static $LOG_LEVEL_DEBUG = 100;
    static $LOG_LEVEL_INFO = 200;
    static $LOG_LEVEL_WARNINGG = 300;
    static $LOG_LEVEL_ERROR = 400;
    static $LOG_LEVEL_CRITICAL = 500;
    static $LOG_LEVEL_ALERT = 550;

    static $FEATURE_ROLLOUT = 'FEATURE_ROLLOUT';
    static $FEATURE_TEST = 'FEATURE_TEST';
    static $AB = 'VISUAL_AB';
    static $apiName;
    /**
     * @var mixed|string to save settings
     */
    var $settings = '';
    /**
     * @var Connection to save connection object for curl requests
     */
    var $connection;
    /**
     * @var string to save userStorage interface object
     */

    var $_userStorageObj;
    /**
     * @var int to save if dev mode is enabled or not
     */
    var $development_mode;

    /**
     * VWO constructor.
     *
     * @param  $config
     * @throws Exception
     */
    function __construct($config)
    {
        self::$apiName = 'init';
        if (!is_array($config)) {
            return (object)[];
        }
        // is settings and logger files are provided then set the values to the object
        $settings = isset($config['settingsFile']) ? $config['settingsFile'] : '';
        $logger = isset($config['logging']) ? $config['logging'] : null;

        // dev mode enable wont send tracking hits to the servers
        $this->development_mode = (isset($config['isDevelopmentMode']) && $config['isDevelopmentMode'] == 1) ? 1 : 0;

        if ($logger == null) {
            self::$_logger = new VWOLogger(Logger::DEBUG, 'php://stdout');
        } elseif ($logger instanceof LoggerInterface) {
            self::$_logger = $logger;
            self::addLog(Logger::DEBUG, Constants::DEBUG_MESSAGES['CUSTOM_LOGGER_USED']);
        }

        // user storage service
        if (isset($config['userStorageService']) && ($config['userStorageService'] instanceof UserStorageInterface)) {
            $this->_userStorageObj = $config['userStorageService'];
        } else {
            $this->_userStorageObj = '';
        }

        // initial logging started for each new object
        self::addLog(
            Logger::DEBUG,
            Constants::DEBUG_MESSAGES['SET_DEVELOPMENT_MODE'],
            ['{devmode}' => $this->development_mode]
        );

        $res = Validations::checkSettingSchema($settings);
        if ($res) {
            $this->settings = Campaign::makeRanges($settings);
        } else {
            return [];
        }

        $this->connection = new Connection();
        self::addLog(Logger::DEBUG, Constants::DEBUG_MESSAGES['SDK_INITIALIZED']);
        return $this;
    }

    /**
     * function to addlog to the default/ custom logger
     *
     * @param  $level
     * @param  $message
     * @param array $params
     * @param string $classname
     * @return bool
     */
    static function addLog($level, $message, $params = [], $classname = '')
    {
        try {
            if (self::$_logger == null) {
                self::$_logger = new VWOLogger(Logger::DEBUG, 'php://stdout');
            }
            if (empty($classname)) {
                $classname = self::name();
            }
            $message = Common::makelogMessage($message, $params, $classname, self::$apiName);
            self::$_logger->addLog($message, $level);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return true;
    }

    static function name()
    {
        return 'vwo\VWO';
    }

    /**
     * @param  $accountId
     * @param  $sdkKey
     * @return bool|mixed
     */
    public static function getSettingsFile($accountId, $sdkKey)
    {
        try {
            $connection = new Connection();
            $params = array(
                'a' => $accountId,
                'i' => $sdkKey,
                'r' => time() / 10,
                'platform' => 'server',
                'api-version' => 2
            );
            return $settings = $connection->get(Constants::SETTINGS_URL, $params);
        } catch (Exception $e) {
        }
        return false;
    }

    /**
     * @param  $campaignKey
     * @param  $userId
     * @param  $options
     * @return bool|null
     */
    public function isFeatureEnabled($campaignKey, $userId, $options = [])
    {
        try {
            self::$apiName = 'isFeatureEnabled';
            self::addLog(
                Logger::INFO,
                Constants::INFO_MESSAGES['API_CALLED'],
                ['{api}' => 'isFeatureEnabled', '{userId}' => $userId]
            );
            // get campaigns
            $campaign = Validations::getCampaignFromCampaignKey($campaignKey, $this->settings);
            if ($campaign['type'] == self::$AB) {
                self::addLog(
                    Logger::ERROR,
                    Constants::ERROR_MESSAGE['INVALID_CAMPAIGN_FOR_API'],
                    ['{api}' => 'isFeatureEnabled', '{campaignType}' => $campaign['type'], '{userId}' => $userId]
                );
                return false;
            }
            $result = $this->fetchFeatureEnabledData($campaignKey, $userId, $options);
            if ($result !== false && isset($result['response']) && $result['response'] == true && isset($result['variationData'])) {
                $bucketInfo = $result['variationData'];
                if ($bucketInfo !== null && $campaign['type'] != self::$FEATURE_ROLLOUT) {
                    $this->addVisitor($campaign, $userId, $bucketInfo['id']);
                }
                if ($bucketInfo !== null && $result['response']) {
                    self::addLog(
                        Logger::INFO,
                        Constants::INFO_MESSAGES['FEATURE_ENABLED_FOR_USER'],
                        ['{featureKey}' => $campaignKey, '{userId}' => $userId]
                    );
                    return true;
                }
            }
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
        self::addLog(
            Logger::INFO,
            Constants::INFO_MESSAGES['FEATURE_NOT_ENABLED_FOR_USER'],
            ['{featureKey}' => $campaignKey, '{userId}' => $userId]
        );
        return false;
    }

    /**
     * @param  $campaignKey
     * @param  $userId
     * @param array $variableData
     * @return array - array of selected variation and result in response param
     */
    private function fetchFeatureEnabledData($campaignKey, $userId, $options)
    {
        $result['response'] = false;
        // get campaign
        $campaign = Validations::getCampaignFromCampaignKey($campaignKey, $this->settings);
        // validate the parameters and settings
        if (
            Validations::validateIsFeatureEnabledParams($campaignKey, $userId) && Validations::checkSettingSchema(
                $this->settings
            )
        ) {
            $variationData = $this->fetchVariationData($campaign, $userId, $options);
            $result['variationData'] = $variationData;
            // below condition says that if bucket is there and isFeatureEnabled is not present it means it will be feature rollout type campaign and return true
            // if isFeatureEnabled is there and it must be true then result is true
            // else return to false
            $result['response'] = ((isset($variationData['bucketInfo']) && !isset($variationData['bucketInfo']['isFeatureEnabled'])) || (isset($variationData['bucketInfo']['isFeatureEnabled']) && $variationData['bucketInfo']['isFeatureEnabled']) == true) ? true : false;
        }
        return $result;
    }

    /**
     * @param $campaign
     * @param $usesrId
     * @param array $options
     * @return array|mixed|null
     */
    private function fetchVariationData($campaign, $userId, $options = [])
    {
        $bucketInfo = null;
        if ($campaign !== null) {
            //check for whitelisting if applied and get Variation Info
            $bucketInfo = Common::findVariationFromWhiteListing($campaign, $userId, $options);
            // do murmur operations and get Variation for the customer
            if ($bucketInfo == null) {
                $bucketInfo = $this->userStorageGet($userId, $campaign);
                if ($bucketInfo == null) {
                    //check for pre-segmentation if applied
                    $result = Validations::checkPreSegmentation($campaign, $userId, $options);
                    if ($result == false) {
                        self::addLog(
                            Logger::INFO,
                            Constants::INFO_MESSAGES['INVALID_VARIATION_KEY'],
                            [
                                '{userId}' => $userId,
                                '{variationName}' => $bucketInfo['name'],
                                '{campaignTestKey}' => $campaign['key']
                            ]
                        );

                        return null;
                    }
                    $bucketInfo = Bucketer::getBucket($userId, $campaign);
                    if ($bucketInfo == null) {
                        return null;
                    }
                    $this->userStorageSet($userId, $campaign['key'], $bucketInfo);
                } else {
                    self::addLog(
                        Logger::DEBUG,
                        Constants::DEBUG_MESSAGES['GETTING_STORED_VARIATION'],
                        [
                            '{userId}' => $userId,
                            '{variationName}' => $bucketInfo['name'],
                            '{campaignTestKey}' => $campaign['key']
                        ]
                    );
                }
            }
        }
        return $bucketInfo;
    }

    /***
     * @param  $userId
     * @param  $campaignKey
     * @return array|null
     */
    private function userStorageGet($userId, $campaign)
    {
        try {
            if (!empty($this->_userStorageObj)) {
                $campaignKey = $campaign['key'];
                $variationInfo = $this->_userStorageObj->get($userId, $campaignKey);
                if (
                    isset($variationInfo['variationName']) && is_string(
                        $variationInfo['variationName']
                    ) && !empty($variationInfo['variationName']) && array_key_exists('campaignKey', $variationInfo) && $variationInfo['campaignKey'] == $campaignKey
                ) {
                    self::addLog(
                        Logger::INFO,
                        Constants::INFO_MESSAGES['GETTING_UP_USER_STORAGE_SERVICE'],
                        ['{userId}' => $userId]
                    );
                    if ($campaign !== null) {
                        return $bucketInfo = Bucketer::getBucketVariationId(
                            $campaign,
                            $variationInfo['variationName']
                        );
                    }
                } else {
                    self::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['GET_USER_STORAGE_SERVICE_FAILED'], ['{userId}' => $userId]);
                }
            } else {
                self::addLog(Logger::DEBUG, Constants::DEBUG_MESSAGES['NO_USER_STORAGE_SERVICE_GET'], ['{userId}' => $userId]);
            }
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
        return null;
    }

    private function userStorageSet($userId, $campaignKey, $variation)
    {
        try {
            if (!empty($this->_userStorageObj)) {
                $campaignInfo = Common::getUserData($campaignKey, $variation, $userId);
                $this->_userStorageObj->set($campaignInfo);
                self::addLog(
                    Logger::INFO,
                    Constants::INFO_MESSAGES['SETTING_DATA_USER_STORAGE_SERVICE'],
                    ['{userId}' => $userId]
                );
            } else {
                self::addLog(Logger::DEBUG, Constants::DEBUG_MESSAGES['NO_USER_STORAGE_SERVICE_SET']);
            }
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
    }

    /***
     * API to send add visitor hit to vwo
     *
     * @param  $campaign
     * @param  $customerHash
     * @return mixed
     */
    private function addVisitor($campaign, $userId, $varientId)
    {
        try {
            if ($this->development_mode) {
                $response['status'] = 'success';
                return true;
            } else {
                $params = array(
                    'experiment_id' => $campaign['id'],
                    'combination' => $varientId, // variation id
                    'ed' => '{"p":"server"}',
                );
                $parameters = Common::mergeCommonQueryParams($this->settings['accountId'], $userId, $params);

                $response = $this->connection->get(Constants::TRACK_URL, $parameters);
            }
            self::addLog(
                Logger::INFO,
                Constants::INFO_MESSAGES['IMPRESSION_FOR_TRACK_USER'],
                ['{properties}' => json_encode($parameters)]
            );

            if (isset($response['httpStatus']) && $response['httpStatus'] == 200) {
                self::addLog(
                    Logger::INFO,
                    Constants::INFO_MESSAGES['IMPRESSION_SUCCESS'],
                    [
                        '{userId}' => $userId,
                        '{endPoint}' => 'track-user',
                        '{campaignId}' => $campaign['id'],
                        '{variationId}' => $varientId,
                        '{accountId}' => $this->settings['accountId']
                    ]
                );

                return true;
            }
            self::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['IMPRESSION_FAILED'], ['{endPoint}' => 'addvistior']);
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
        return false;
    }

    /**
     * @param  $campaignKey
     * @param  $variableKey
     * @param  $userId
     * @return bool|float|int|null|string
     */
    public function getFeatureVariableValue($campaignKey, $variableKey, $userId, $options = [])
    {
        try {
            self::$apiName = 'getFeatureVariableValue';
            $campaign = Validations::getCampaignFromCampaignKey($campaignKey, $this->settings);
            if ($campaign != null && $campaign['type'] == self::$AB) {
                self::addLog(
                    Logger::ERROR,
                    Constants::ERROR_MESSAGE['INVALID_API_CALL'],
                    [
                        '{api}' => 'getFeatureVariableValue',
                        '{userId}' => $userId,
                        '{campaignKey}' => $campaignKey,
                        '{campaignType}' => 'SERVER AB'
                    ]
                );
                return false;
            }
            $value = null;
            $featureData = $this->fetchFeatureEnabledData($campaignKey, $userId, $options);
            if ($featureData) {
                if (isset($featureData['variationData'])) {
                    if ($campaign['type'] == self::$FEATURE_ROLLOUT) {
                        $featureVariable = $campaign['variables'];
                    } else {
                        // it is part of feature test
                        if ($featureData['response'] == 1 && isset($featureData['variationData']['variables'])) {
                            $featureVariable = $featureData['variationData']['variables'];
                        } else {
                            $featureVariable = Common::fetchControlVariation(
                                $campaign['variations']
                            )['variables'];
                        }
                    }
                    $value = Common::fetchVariableValueFromCampaign($featureVariable, $variableKey);
                }
            }
            if ($value == null) {
                self::addLog(
                    Logger::DEBUG,
                    Constants::INFO_MESSAGES['VARIABLE_NOT_FOUND'],
                    [
                        '{userId}' => $userId,
                        '{variableKey}' => $variableKey,
                        '{campaignKey}' => $campaignKey,
                        '{variableValue}' => $value
                    ]
                );
            } else {
                self::addLog(
                    Logger::DEBUG,
                    Constants::INFO_MESSAGES['VARIABLE_FOUND'],
                    [
                        '{userId}' => $userId,
                        '{variableKey}' => $variableKey,
                        '{campaignKey}' => $campaignKey,
                        '{variableValue}' => $value
                    ]
                );
            }
            return $value;
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
    }

    /**
     * API for track the user goals and revenueValue
     *
     * @param string $campaignKey
     * @param string $userId
     * @param string $goalName
     * @param array $options
     * @return bool|null
     */
    public function track($campaignKey = '', $userId = '', $goalName = '', $options = [])
    {
        try {
            self::$apiName = 'track';
            $revenueValue = Common::getValueFromOptions($options, 'revenueValue');
            $bucketInfo = null;
            if (empty($campaignKey) || empty($userId) || empty($goalName)) {
                self::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['TRACK_API_MISSING_PARAMS']);
                return false;
            }
            $campaign = Validations::getCampaignFromCampaignKey($campaignKey, $this->settings);
            if ($campaign !== null) {
                if ($campaign['type'] == self::$FEATURE_ROLLOUT) {
                    self::addLog(
                        Logger::ERROR,
                        Constants::ERROR_MESSAGE['INVALID_API_CALL'],
                        [
                            '{api}' => 'track',
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaignKey,
                            '{campaignType}' => $campaign['type']
                        ]
                    );
                    return false;
                }
                $bucketInfo = Common::findVariationFromWhiteListing($campaign, $userId, $options);
                if ($bucketInfo == null) {
                    $bucketInfo = $this->userStorageGet($userId, $campaign);
                    if ($bucketInfo == null) {
                        $result = Validations::checkPreSegmentation($campaign, $userId, $options);
                        if ($result == false) {
                            return null;
                        }
                        $bucketInfo = Bucketer::getBucket($userId, $campaign);
                    }
                    if ($bucketInfo == null) {
                        return null;
                    }
                    self::addLog(
                        Logger::DEBUG,
                        Constants::DEBUG_MESSAGES['NO_STORED_VARIATION'],
                        ['{userId}' => $userId, '{campaignTestKey}' => $campaignKey]
                    );
                    $this->userStorageSet($userId, $campaignKey, $bucketInfo);
                } else {
                    self::addLog(
                        Logger::DEBUG,
                        Constants::DEBUG_MESSAGES['GETTING_STORED_VARIATION'],
                        [
                            '{userId}' => $userId,
                            '{variationName}' => $bucketInfo['name'],
                            '{campaignTestKey}' => $campaignKey
                        ]
                    );
                }

                $goal = Common::getGoal($campaign['goals'], $goalName);
                $goalId = isset($goal['id']) ? $goal['id'] : 0;
                if ($goalId && isset($bucketInfo['id']) && $bucketInfo['id'] > 0) {
                    if ($this->development_mode) {
                        return true;
                    } else {
                        $params = array(
                            'experiment_id' => $campaign['id'],
                            'combination' => $bucketInfo['id'],
                            'goal_id' => $goalId
                        );
                        $parameters = Common::mergeCommonQueryParams($this->settings['accountId'], $userId, $params);
                        if ($goal['type'] == "REVENUE_TRACKING" && is_null($revenueValue)) {
                            self::addLog(
                                Logger::ERROR,
                                Constants::ERROR_MESSAGE['MISSING_GOAL_REVENUE'],
                                [
                                    '{goalIdentifier}' => $goalName,
                                    '{campaignTestKey}' => $campaignKey,
                                    '{userId}' => $userId
                                ]
                            );
                            return false;
                        }
                        if (
                            $goal['type'] == "REVENUE_TRACKING" && (is_string($revenueValue) || is_float(
                                $revenueValue
                            ) || is_int($revenueValue))
                        ) {
                            $parameters['r'] = $revenueValue;
                        }
                        $response = $this->connection->get(Constants::GOAL_URL, $parameters);
                    }
                    self::addLog(
                        Logger::INFO,
                        Constants::INFO_MESSAGES['IMPRESSION_FOR_TRACK_GOAL'],
                        array('{properties}' => json_encode($parameters))
                    );
                    if (isset($response['httpStatus']) && $response['httpStatus'] == 200) {
                        self::addLog(
                            Logger::INFO,
                            Constants::INFO_MESSAGES['IMPRESSION_SUCCESS'],
                            [
                                '{userId}' => $userId,
                                '{endPoint}' => 'track-goal',
                                '{campaignId}' => $campaign['id'],
                                '{variationId}' => $bucketInfo['id'],
                                '{accountId}' => $this->settings['accountId']
                            ]
                        );
                        return true;
                    }
                    self::addLog(
                        Logger::ERROR,
                        Constants::ERROR_MESSAGE['IMPRESSION_FAILED'],
                        ['{endPoint}' => 'trackGoal']
                    );
                } else {
                    self::addLog(
                        Logger::ERROR,
                        Constants::ERROR_MESSAGE['TRACK_API_GOAL_NOT_FOUND'],
                        ['{campaignTestKey}' => $campaignKey, '{userId}' => $userId]
                    );
                }
            }
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
        return false;
    }

    /**
     * to send variation name along with api hit to send add visitor hit
     *
     * @param  $campaignKey
     * @param  $userId
     * @param  $options
     * @return string|null
     */
    public function activate($campaignKey, $userId, $options = [])
    {
        self::$apiName = 'activate';
        self::addLog(
            Logger::INFO,
            Constants::INFO_MESSAGES['API_CALLED'],
            ['{api}' => 'activate', '{userId}' => $userId]
        );
        return $this->getVariation($campaignKey, $userId, $options, 1);
    }

    /**
     * fetch the variation name
     *
     * @param  $campaignKey
     * @param  $customerHash
     * @param int $addVisitor
     * @return null| bucketname
     */
    private function getVariation($campaignKey, $userId, $options = [], $addVisitor = 0)
    {
        $bucketInfo = null;
        try {
            $campaign = Validations::getCampaignFromCampaignKey($campaignKey, $this->settings);
            if ($campaign !== null) {
                if (($campaign['type'] == self::$FEATURE_ROLLOUT) || ($campaign['type'] == self::$FEATURE_TEST && $addVisitor == 1)) {
                    self::addLog(
                        Logger::ERROR,
                        Constants::ERROR_MESSAGE['INVALID_API_CALL'],
                        [
                            '{api}' => $addVisitor == 1 ? 'activate' : 'getVariationName',
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaignKey,
                            '{campaignType}' => $campaign['type']
                        ]
                    );
                    return false;
                }
            }
            $bucketInfo = $this->fetchVariationData($campaign, $userId, $options);
            if ($bucketInfo !== null) {
                if ($addVisitor) {
                    $this->addVisitor($campaign, $userId, $bucketInfo['id']);
                }
                return $bucketInfo['name'];
            }
        } catch (Exception $e) {
            self::addLog(Logger::ERROR, $e->getMessage());
        }
        return null;
    }

    /**
     * to send variation name along with api hit to send add visitor hit
     *
     * @param  $campaignKey
     * @param  $userId
     * @return string|null
     */
    public function getVariationName($campaignKey, $userId, $options = [])
    {
        self::$apiName = 'getVariation';
        self::addLog(
            Logger::INFO,
            Constants::INFO_MESSAGES['API_CALLED'],
            ['{api}' => 'getVariation', '{userId}' => $userId]
        );
        return $this->getVariation($campaignKey, $userId, $options, 0);
    }

    /**
     * @param  $tagKey
     * @param  $tagValue
     * @param  $userId
     * @return bool
     */
    public function push($tagKey, $tagValue, $userId)
    {
        self::$apiName = 'push';
        //Check if tagName and tagValue are defined and are string, otherwise log and return FALSE
        if (
            Validations::pushApiParams($tagKey, $tagValue, $userId) && Validations::checkSettingSchema(
                $this->settings
            )
        ) {
            //Send a call to VWO server
            try {
                if ($this->development_mode) {
                    return true;
                } else {
                    $params = array(
                        'tags' => '{"u":{"' . $tagKey . '":"' . $tagValue . '"}}'
                    );
                    $parameters = Common::mergeCommonQueryParams($this->settings['accountId'], $userId, $params);
                    $response = $this->connection->get(Constants::PUSH_URL, $parameters);
                }
                self::addLog(
                    Logger::INFO,
                    Constants::INFO_MESSAGES['IMPRESSION_FOR_PUSH'],
                    ['{properties}' => json_encode($parameters)]
                );
                if (isset($response['httpStatus']) && $response['httpStatus'] == 200) {
                    self::addLog(
                        Logger::INFO,
                        Constants::INFO_MESSAGES['IMPRESSION_SUCCESS_PUSH'],
                        [
                            '{userId}' => $userId,
                            '{endPoint}' => 'push',
                            '{accountId}' => $this->settings['accountId'],
                            '{tags}' => $parameters['tags']
                        ]
                    );
                    return true;
                }
                self::addLog(Logger::ERROR, Constants::ERROR_MESSAGE['IMPRESSION_FAILED'], ['{endPoint}' => 'push']);
            } catch (Exception $e) {
                self::addLog(Logger::ERROR, $e->getMessage());
            }
        }
        return false;
    }
}
