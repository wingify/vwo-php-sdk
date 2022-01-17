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

namespace vwo;

use Exception as Exception;
use vwo\Constants\Constants as Constants;
use vwo\Constants\EventEnum;
use vwo\Constants\FileNameEnum;
use vwo\Constants\Urls;
use vwo\Constants\Urls as UrlConstants;
use vwo\Constants\CampaignTypes;
use vwo\Services\HooksManager;
use vwo\Services\UsageStats;
use vwo\Storage\UserStorageInterface;
use vwo\Utils\AccountUtil;
use vwo\Utils\Campaign as CampaignUtil;
use vwo\Utils\Common as CommonUtil;
use vwo\Utils\DataLocationManager;
use vwo\Utils\Validations as ValidationsUtil;
use vwo\Utils\ImpressionBuilder as ImpressionBuilder;
use vwo\Utils\EventDispatcher as EventDispatcher;
use Monolog\Logger as Logger;
use vwo\Logger\LoggerInterface;
use vwo\Services\LoggerService as LoggerService;
use vwo\Logger\VWOLogger as VWOLogger;
use vwo\Core\Bucketer as Bucketer;
use vwo\Core\VariationDecider as VariationDecider;
use vwo\Utils\LogMessagesUtil;

/***
 * Class for exposing various APIs
 */
class VWO
{
    /****
     * @var static variables for log levels
     */

    // Levels are as per monolog docs - https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#log-levels
    static $LOG_LEVEL_DEBUG = 100;
    static $LOG_LEVEL_INFO = 200;
    static $LOG_LEVEL_WARNINGG = 300;
    static $LOG_LEVEL_ERROR = 400;

    const CLASSNAME = FileNameEnum::VWO;

    static $apiName;

    static $_variationDecider;
    /**
     * @var mixed|string to save settings
     */
    var $settings = '';
    /**
     * @var Connection to save connection object for curl requests
     */
    // var $connection;
    /**
     * @var string to save userStorage interface object
     */

    var $_userStorageObj;
    /**
     * @var int to save if dev mode is enabled or not
     */
    var $isDevelopmentMode;

    private $goalTypeToTrack;

    private $isOptedOut = false;

    const GOAL_TYPES = [
        'REVENUE' => 'REVENUE_TRACKING',
        'CUSTOM' => 'CUSTOM_GOAL',
        'ALL' => 'ALL'
    ];

    /**
     * VWO constructor.
     *
     * @param  $config
     * @throws Exception
     */
    function __construct($config)
    {
        self::$apiName = 'init';
        LoggerService::setApiName(self::$apiName);
        if (!is_array($config)) {
            return (object)[];
        }

        if (!ValidationsUtil::validateSDKConfiguration($config, self::$apiName)) {
            LoggerService::log(Logger::ERROR, 'CONFIG_CORRUPTED', [], self::CLASSNAME);
            return (object)[];
        }
        LogMessagesUtil::instance();

        $usageStats = [];
        // is settings and logger files are provided then set the values to the object
        $settings = isset($config['settingsFile']) ? $config['settingsFile'] : '';
        $logger = isset($config['logging']) ? $config['logging'] : null;
        if ($settings) {
            DataLocationManager::instance()->setSettings($settings);
        }

        // dev mode enable wont send tracking hits to the servers
        $this->isDevelopmentMode = (isset($config['isDevelopmentMode']) && $config['isDevelopmentMode'] == 1) ? 1 : 0;

        $this->eventDispatcher = new EventDispatcher($this->isDevelopmentMode);

        if ($logger == null) {
            $_logger = new VWOLogger(Logger::DEBUG, 'php://stdout');

            LoggerService::setLogger($_logger);
        } elseif ($logger instanceof LoggerInterface) {
            LoggerService::setLogger($logger);
            LoggerService::log(Logger::DEBUG, 'CONFIG_CUSTOM_LOGGER_USED', [], self::CLASSNAME);
            $usageStats['cl'] = 1;
        }

        // user storage service
        if (isset($config['userStorageService']) && ($config['userStorageService'] instanceof UserStorageInterface)) {
            $this->_userStorageObj = $config['userStorageService'];
            $usageStats['ss'] = 1;
        } else {
            $this->_userStorageObj = '';
        }

        if (isset($config['goalTypeToTrack'])) {
            if (array_key_exists($config['goalTypeToTrack'], self::GOAL_TYPES)) {
                $this->goalTypeToTrack = $config['goalTypeToTrack'];
                $usageStats['gt'] = 1;
            } else {
                LoggerService::log(Logger::ERROR, 'CONFIG_PARAMETER_INVALID', ['{parameter}' => 'goalTypeToTrack', '{api}' => self::$apiName, '{type}' => 'strings(REVENUE, CUSTOM, ALL)'], self::CLASSNAME);
            }
        } else {
            $this->goalTypeToTrack = 'ALL';
        }

        // initial logging started for each new object
        if ($this->isDevelopmentMode) {
            LoggerService::log(
                Logger::DEBUG,
                'CONFIG_DEVELOPMENT_MODE_STATUS',
                [],
                self::CLASSNAME
            );
        }

        $res = ValidationsUtil::checkSettingSchema($settings);
        if ($res) {
            $this->settings = CampaignUtil::makeRanges($settings);
            LoggerService::log(
                Logger::DEBUG,
                'SETTINGS_FILE_PROCESSED',
                ['{accountId}' => $this->settings['accountId']],
                self::CLASSNAME
            );
        } else {
            LoggerService::log(Logger::ERROR, 'SETTINGS_FILE_INVALID', [], self::CLASSNAME);
            return (object)[];
        }

        // $this->connection = new Connection();
        LoggerService::log(Logger::INFO, 'SDK_INITIALIZED', [], self::CLASSNAME);

        $this->variationDecider = new VariationDecider($this->settings);
        if (isset($this->settings['accountId'])) {
            $this->variationDecider->setAccountId($this->settings['accountId']);
            $accountUtil = AccountUtil::instance();
            $accountUtil->setAccountId($this->settings['accountId']);
        }
        // Initialize Hooks manager so that callbacks can be invoked
        $this->variationDecider->setHooksManager(new HooksManager($config));

        $this->usageStats = new UsageStats($usageStats, $config, $this->isDevelopmentMode);
        return $this;
    }

    /**
     * @param  String|Integer $accountId
     * @param  String         $sdkKey
     * @param  bool           $isTriggeredByWebhook
     * @return bool|mixed
     */
    public static function getSettingsFile($accountId, $sdkKey, $isTriggeredByWebhook = false)
    {
        self::$apiName = 'getSettingsFile';
        LoggerService::setApiName(self::$apiName);
        if (!$accountId || !$sdkKey) {
            LoggerService::log(Logger::ERROR, 'MISSING_IMPORT_SETTINGS_MANDATORY_PARAMS', [], self::CLASSNAME);
            return false;
        }
        try {
            $parameters = ImpressionBuilder::getSettingsFileQueryParams($accountId, $sdkKey);
            $eventDispatcher = new EventDispatcher(false);

            if ($isTriggeredByWebhook) {
                $url = UrlConstants::WEBHOOK_SETTINGS_URL;
            } else {
                $url = UrlConstants::SETTINGS_URL;
            }

            return $eventDispatcher->send($url, $parameters);
        } catch (Exception $e) {
            LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
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
        self::$apiName = 'isFeatureEnabled';
        LoggerService::setApiName(self::$apiName);

        if ($this->isOptedOut()) {
            return false;
        }

        try {
            if (!ValidationsUtil::validateIsFeatureEnabledParams($campaignKey, $userId, self::$apiName)) {
                LoggerService::log(Logger::ERROR, 'API_BAD_PARAMETERS', ['{api}' => self::$apiName], self::CLASSNAME);
                return null;
            }
            // get campaigns
            $campaign = ValidationsUtil::getCampaignFromCampaignKey($campaignKey, $this->settings, self::$apiName);
            if ($campaign == null) {
                return null;
            }
            if ($campaign['type'] == CampaignTypes::AB) {
                LoggerService::log(
                    Logger::WARNING,
                    'CAMPAIGN_NOT_RUNNING',
                    ['{api}' => 'isFeatureEnabled', '{campaignKey}' => $campaignKey, '{userId}' => $userId],
                    self::CLASSNAME
                );
                return null;
            }

            $result['response'] = false;
            $variationData = $this->variationDecider->fetchVariationData($this->_userStorageObj, $campaign, $userId, $options, self::$apiName);
            // below condition says that if bucket is there and isFeatureEnabled is not present it means it will be feature rollout type campaign and return true
            // if isFeatureEnabled is there and it must be true then result is true
            // else return to false
            $result['response'] = ((isset($variationData) && !isset($variationData['isFeatureEnabled'])) || (isset($variationData['isFeatureEnabled']) && $variationData['isFeatureEnabled']) == true) ? true : false;

            if ($variationData) {
                if ($this->isEventArchEnabled()) {
                    $parameters = ImpressionBuilder::getEventsBaseProperties($this->settings['accountId'], $this->getSDKKey(), EventEnum::VWO_VARIATION_SHOWN, $this->usageStats->getUsageStats());
                    $payload = ImpressionBuilder::getTrackUserPayloadData(
                        $this->settings,
                        $userId,
                        EventEnum::VWO_VARIATION_SHOWN,
                        $campaign['id'],
                        $variationData['id']
                    );
                } else {
                    $parameters = ImpressionBuilder::getVisitorQueryParams(
                        $this->settings['accountId'],
                        $campaign,
                        $userId,
                        $variationData['id'],
                        $this->getSDKKey()
                    );
                    $parameters = array_merge($parameters, $this->usageStats->getUsageStats());
                }
            }

            if (isset($variationData) && $result['response'] == false) {
                if ($this->isEligibleToSendImpressionToVWO()) {
                    if ($this->isEventArchEnabled()) {
                        $response = $this->eventDispatcher->sendPost($parameters, $payload);
                    } else {
                        LoggerService::log(
                            Logger::DEBUG,
                            'IMPRESSION_FOR_TRACK_USER',
                            ['{properties}' => $this->getAllowedToLogImpressionParams($parameters)],
                            self::CLASSNAME
                        );
                        $response = $this->eventDispatcher->sendAsyncRequest(CommonUtil::getUrl(Urls::TRACK_USER_ENDPOINT), 'GET', $parameters);
                    }
                    LoggerService::log(
                        Logger::INFO,
                        'FEATURE_STATUS',
                        ['{campaignKey}' => $campaignKey, '{userId}' => $userId, '{status}' => 'disabled'],
                        self::CLASSNAME
                    );
                    if ($response) {
                        LoggerService::log(
                            Logger::INFO,
                            'IMPRESSION_SUCCESS',
                            [
                                '{endPoint}' => 'track-user',
                                '{mainKeys}' => json_encode(["campaignId" => $campaign['id']]),
                                '{accountId}' => $this->settings['accountId']
                            ],
                            self::CLASSNAME
                        );
                    }
                } else {
                    LoggerService::log(
                        Logger::INFO,
                        'CAMPAIGN_USER_ALREADY_TRACKED',
                        ['{userId}' => $userId, '{campaignKey}' => $campaignKey, '{api}' => self::$apiName],
                        self::CLASSNAME
                    );
                }
                return false;
            }
            if ($result !== false && isset($result['response']) && $result['response'] == true && isset($variationData)) {
                if ($this->isEligibleToSendImpressionToVWO()) {
                    if ($this->isEventArchEnabled()) {
                        $response = $this->eventDispatcher->sendPost($parameters, $payload);
                    } else {
                        LoggerService::log(
                            Logger::DEBUG,
                            'IMPRESSION_FOR_TRACK_USER',
                            ['{properties}' => $this->getAllowedToLogImpressionParams($parameters)],
                            self::CLASSNAME
                        );
                        $response = $this->eventDispatcher->sendAsyncRequest(CommonUtil::getUrl(Urls::TRACK_USER_ENDPOINT), 'GET', $parameters);
                    }
                    LoggerService::log(
                        Logger::INFO,
                        'FEATURE_STATUS',
                        ['{campaignKey}' => $campaignKey, '{userId}' => $userId, '{status}' => 'enabled'],
                        self::CLASSNAME
                    );

                    if ($response) {
                        LoggerService::log(
                            Logger::INFO,
                            'IMPRESSION_SUCCESS',
                            [
                                '{mainKeys}' => json_encode(["campaignId" => $campaign['id']]),
                                '{endPoint}' => Urls::TRACK_USER_ENDPOINT,
                                '{campaignId}' => $campaign['id'],
                                '{accountId}' => $this->settings['accountId']
                            ],
                            self::CLASSNAME
                        );
                    }
                } else {
                    LoggerService::log(
                        Logger::INFO,
                        'CAMPAIGN_USER_ALREADY_TRACKED',
                        ['{userId}' => $userId, '{campaignKey}' => $campaignKey, '{api}' => self::$apiName],
                        self::CLASSNAME
                    );
                }
                return true;
            }
            return $campaign['type'] == CampaignTypes::FEATURE_ROLLOUT ? false : null;
        } catch (Exception $e) {
            LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
        }

        return isset($campaign) && isset($campaign['type']) && ($campaign['type'] == CampaignTypes::FEATURE_ROLLOUT) ? false : null;
    }

    /**
     * @param  $campaignKey
     * @param  $variableKey
     * @param  $userId
     * @return bool|float|int|null|string
     */
    public function getFeatureVariableValue($campaignKey, $variableKey, $userId, $options = [])
    {
        self::$apiName = 'getFeatureVariableValue';
        LoggerService::setApiName(self::$apiName);

        if ($this->isOptedOut()) {
            return false;
        }

        try {
            if (!ValidationsUtil::validateIsFeatureEnabledParams($campaignKey, $userId, self::$apiName)) {
                LoggerService::log(Logger::ERROR, 'API_BAD_PARAMETERS', ['{api}' => self::$apiName], self::CLASSNAME);
                return null;
            }

            $campaign = ValidationsUtil::getCampaignFromCampaignKey($campaignKey, $this->settings, self::$apiName);
            if ($campaign != null && $campaign['type'] == CampaignTypes::AB) {
                LoggerService::log(
                    Logger::ERROR,
                    'API_NOT_APPLICABLE',
                    [
                        '{api}' => 'getFeatureVariableValue',
                        '{userId}' => $userId,
                        '{campaignKey}' => $campaignKey,
                        '{campaignType}' => 'SERVER AB'
                    ],
                    self::CLASSNAME
                );
                return null;
            }
            $value = null;

            $featureData['response'] = false;
            $variationData = $this->variationDecider->fetchVariationData($this->_userStorageObj, $campaign, $userId, $options, self::$apiName);
            $featureData['variationData'] = $variationData;
            // below condition says that if bucket is there and isFeatureEnabled is not present it means it will be feature rollout type campaign and return true
            // if isFeatureEnabled is there and it must be true then result is true
            // else return to false
            $featureData['response'] = ((isset($variationData) && !isset($variationData['isFeatureEnabled'])) || (isset($variationData['isFeatureEnabled']) && $variationData['isFeatureEnabled']) == true) ? true : false;

            if ($featureData) {
                if (isset($featureData['variationData'])) {
                    if ($campaign['type'] == CampaignTypes::FEATURE_ROLLOUT) {
                        $featureVariable = $campaign['variables'];
                    } else {
                        // it is part of feature test
                        if ($featureData['response'] == 1 && isset($featureData['variationData']['variables'])) {
                            $featureVariable = $featureData['variationData']['variables'];
                        } else {
                            $featureVariable = CommonUtil::fetchControlVariation(
                                $campaign['variations']
                            )['variables'];
                        }
                    }
                    $value = CommonUtil::getVariableValue($featureVariable, $variableKey);
                }
            }
            if ($value == null) {
                LoggerService::log(
                    Logger::INFO,
                    'FEATURE_VARIABLE_DEFAULT_VALUE',
                    [
                        '{variableKey}' => $variableKey,
                        '{variationName}' => $variationData["name"]
                    ],
                    self::CLASSNAME
                );
            } else {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                LoggerService::log(
                    Logger::INFO,
                    'FEATURE_VARIABLE_VALUE',
                    [
                        '{userId}' => $userId,
                        '{variableKey}' => $variableKey,
                        '{campaignKey}' => $campaignKey,
                        '{variableValue}' => $value
                    ],
                    self::CLASSNAME
                );
            }

            return $value;
        } catch (Exception $e) {
            LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
        }

        return null;
    }

    /**
     * API for track the user goals and revenueValue
     *
     * @param  string $campaignKey
     * @param  string $userId
     * @param  string $goalIdentifier
     * @param  array  $options
     * @return array|bool|null
     */
    public function track($campaignKey = '', $userId = '', $goalIdentifier = '', array $options = [])
    {
        self::$apiName = 'track';
        LoggerService::setApiName(self::$apiName);

        if ($this->isOptedOut()) {
            return false;
        }

        $revenueValue = CommonUtil::getValueFromOptions($options, 'revenueValue');
        $bucketInfo = null;


        if (
            empty($userId)
            || empty($goalIdentifier)
            || !(is_null($campaignKey) || is_array($campaignKey) || is_string($campaignKey))
        ) {
            LoggerService::log(Logger::ERROR, 'API_BAD_PARAMETERS', ['{api}' => self::$apiName], self::CLASSNAME);
            return null;
        }

        $goalTypeToTrack = $this->getGoalTypeToTrack($options);
        $campaigns = ValidationsUtil::getCampaigns($campaignKey, $this->settings, $goalIdentifier, $goalTypeToTrack, self::$apiName);

        if (empty($campaigns)) {
            return null;
        }

        $metricMap = [];
        $revenueProps = [];
        $result = [];
        foreach ($campaigns as $campaign) {
            try {
                if ($campaign['type'] == CampaignTypes::FEATURE_ROLLOUT) {
                    LoggerService::log(
                        Logger::ERROR,
                        'API_NOT_APPLICABLE',
                        [
                            '{api}' => 'track',
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaign['key'],
                            '{campaignType}' => $campaign['type']
                        ],
                        self::CLASSNAME
                    );
                    $result[$campaign['key']] = null;
                    continue;
                }

                $bucketInfo = $this->variationDecider->fetchVariationData($this->_userStorageObj, $campaign, $userId, $options, self::$apiName, $goalIdentifier);
                if ($bucketInfo === null) {
                    $result[$campaign['key']] = null;
                    continue;
                }

                $goal = CommonUtil::getGoalFromGoals($campaign['goals'], $goalIdentifier);
                $goalId = isset($goal['id']) ? $goal['id'] : 0;
                if ($goalId && isset($bucketInfo['id']) && $bucketInfo['id'] > 0) {
                    if ($goal['type'] == "REVENUE_TRACKING" && is_null($revenueValue)) {
                        LoggerService::log(
                            Logger::ERROR,
                            'TRACK_API_REVENUE_NOT_PASSED_FOR_REVENUE_GOAL',
                            [
                                '{goalIdentifier}' => $goalIdentifier,
                                '{campaignKey}' => $campaign['key'],
                                '{userId}' => $userId
                            ],
                            self::CLASSNAME
                        );
                        $result[$campaign['key']] = null;
                        continue;
                    }

                    if (isset($goalIdentifier)) {
                        if (isset($bucketInfo['goalIdentifier'])) {
                            $identifiers = explode("_vwo_", $bucketInfo['goalIdentifier']);
                        } else {
                            $bucketInfo['goalIdentifier'] = '';
                            $identifiers = [];
                        }


                        if (!in_array($goalIdentifier, $identifiers)) {
                            $bucketInfo['goalIdentifier'] .=  "_vwo_$goalIdentifier";
                            if (!empty($this->_userStorageObj)) {
                                $this->variationDecider->userStorageSet($this->_userStorageObj, $userId, $campaign['key'], $bucketInfo, $bucketInfo['goalIdentifier']);
                            }
                        } else {
                            LoggerService::log(
                                Logger::INFO,
                                'CAMPAIGN_GOAL_ALREADY_TRACKED',
                                [
                                    '{goalIdentifier}' => $goalIdentifier,
                                    '{campaignKey}' => $campaign['key'],
                                    '{userId}' => $userId
                                ],
                                self::CLASSNAME
                            );
                            $result[$campaign['key']] = false;
                            continue;
                        }
                    }

                    if ($this->isEventArchEnabled()) {
                        if ($goal['type'] == "REVENUE_TRACKING" && !in_array($goal['revenueProp'], $revenueProps)) {
                            $revenueProps[] = $goal['revenueProp'];
                        }
                        $metricMap[$campaign['id']] = $goal["id"];
                    } else {
                        $parameters = ImpressionBuilder::getConversionQueryParams(
                            $this->settings['accountId'],
                            $campaign,
                            $userId,
                            $bucketInfo['id'],
                            $goal,
                            $revenueValue,
                            $this->getSDKKey()
                        );
                        LoggerService::log(
                            Logger::DEBUG,
                            'IMPRESSION_FOR_TRACK_GOAL',
                            ['{properties}' => $this->getAllowedToLogImpressionParams($parameters)],
                            self::CLASSNAME
                        );
                        $resp = $this->eventDispatcher->sendAsyncRequest(CommonUtil::getUrl(Urls::TRACK_GOAL_ENDPOINT), 'GET', $parameters);
                        if ($resp) {
                            LoggerService::log(
                                Logger::INFO,
                                'IMPRESSION_SUCCESS',
                                [
                                    '{endPoint}' => Urls::TRACK_GOAL_ENDPOINT,
                                    '{mainKeys}' => json_encode(["campaignId" => $campaign['id'], "variationId" => $bucketInfo['id'], "goalId" => $goal['id']]),
                                    '{accountId}' => $this->settings['accountId']
                                ],
                                self::CLASSNAME
                            );
                        }
                    }

                    if ($this->isDevelopmentMode) {
                        $result[$campaign['key']] = true;
                        continue;
                    }

                    $result[$campaign['key']] = true;
                } else {
                    LoggerService::log(
                        Logger::ERROR,
                        'TRACK_API_GOAL_NOT_FOUND',
                        ['{campaignKey}' => $campaign['key'], '{userId}' => $userId, "{goalIdentifier}" => $goalIdentifier],
                        self::CLASSNAME
                    );

                    $result[$campaign['key']] = null;
                }
            } catch (Exception $e) {
                LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
            }
        }

        if ($this->isEventArchEnabled()) {
            $parameters = ImpressionBuilder::getEventsBaseProperties($this->settings['accountId'], $this->getSDKKey(), $goalIdentifier);
            $payload = ImpressionBuilder::getTrackGoalPayloadData(
                $this->settings,
                $userId,
                $goalIdentifier,
                $revenueValue,
                $metricMap,
                $revenueProps
            );
            $this->eventDispatcher->sendPost($parameters, $payload);
            if ($this->isEligibleToSendImpressionToVWO()) {
                LoggerService::log(
                    Logger::INFO,
                    'IMPRESSION_SUCCESS_FOR_EVENT_ARCH',
                    [
                        '{accountId}' => $parameters["a"],
                        '{event}' => 'visitor property:' . json_encode($payload["d"]["visitor"]["props"]),
                        '{endPoint}' => CommonUtil::getEventsUrl()
                    ],
                    self::CLASSNAME
                );
            }
        }

        if (count($result) == 0) {
            return null;
        }
        if (is_string($campaignKey)) {
            return $result[$campaignKey];
        }
        return $result;
    }

    /**
     * to send variation name along with api hit to send add visitor hit
     *
     * @param  string $campaignKey
     * @param  string $userId
     * @param  array  $options
     * @return string|null
     */
    public function activate($campaignKey, $userId, $options = [])
    {
        self::$apiName = 'activate';
        LoggerService::setApiName(self::$apiName);

        if ($this->isOptedOut()) {
            return false;
        }

        return $this->getVariation($campaignKey, $userId, $options, 1, self::$apiName);
    }

    /**
     * fetch the variation name
     *
     * @param  $campaignKey
     * @param  $userId
     * @param  array  $options
     * @param  int    $trackVisitor
     * @param  string $apiName
     * @return null|string
     */
    private function getVariation($campaignKey, $userId, $options, $trackVisitor, $apiName)
    {
        if (empty($userId) || !is_string($campaignKey)) {
            LoggerService::log(Logger::ERROR, 'API_BAD_PARAMETERS', ['{api}' => self::$apiName], self::CLASSNAME);
            return null;
        }
        $bucketInfo = null;
        try {
            $campaign = ValidationsUtil::getCampaignFromCampaignKey($campaignKey, $this->settings, $apiName);
            if ($campaign !== null) {
                if (($campaign['type'] == CampaignTypes::FEATURE_ROLLOUT) || ($campaign['type'] == CampaignTypes::FEATURE_TEST && $trackVisitor == 1)) {
                    LoggerService::log(
                        Logger::ERROR,
                        'API_NOT_APPLICABLE',
                        [
                            '{api}' => $trackVisitor == 1 ? 'activate' : 'getVariationName',
                            '{userId}' => $userId,
                            '{campaignKey}' => $campaignKey,
                            '{campaignType}' => $campaign['type']
                        ],
                        self::CLASSNAME
                    );
                    return $bucketInfo;
                }
            } else {
                return $bucketInfo;
            }
            $bucketInfo = $this->variationDecider->fetchVariationData($this->_userStorageObj, $campaign, $userId, $options, $trackVisitor ? 'activate' : 'getVariationName');
            if ($bucketInfo !== null) {
                if ($trackVisitor) {
                    if ($this->isEligibleToSendImpressionToVWO()) {
                        if ($this->isEventArchEnabled()) {
                            $parameters = ImpressionBuilder::getEventsBaseProperties($this->settings['accountId'], $this->getSDKKey(), EventEnum::VWO_VARIATION_SHOWN, $this->usageStats->getUsageStats());
                            $payload = ImpressionBuilder::getTrackUserPayloadData(
                                $this->settings,
                                $userId,
                                EventEnum::VWO_VARIATION_SHOWN,
                                $campaign['id'],
                                $bucketInfo['id']
                            );
                            $this->eventDispatcher->sendPost($parameters, $payload);
                        } else {
                            $parameters = ImpressionBuilder::getVisitorQueryParams(
                                $this->settings['accountId'],
                                $campaign,
                                $userId,
                                $bucketInfo['id'],
                                $this->getSDKKey()
                            );

                            $parameters =  array_merge($parameters, $this->usageStats->getUsageStats());
                            $this->eventDispatcher->sendAsyncRequest(CommonUtil::getUrl(Urls::TRACK_USER_ENDPOINT), 'GET', $parameters);
                            LoggerService::log(
                                Logger::DEBUG,
                                'IMPRESSION_FOR_TRACK_USER',
                                ['{properties}' => $this->getAllowedToLogImpressionParams($parameters)],
                                self::CLASSNAME
                            );
                        }

                        if (!$this->isDevelopmentMode) {
                            if ($this->isEventArchEnabled()) {
                                LoggerService::log(
                                    Logger::INFO,
                                    'IMPRESSION_SUCCESS_FOR_EVENT_ARCH',
                                    [
                                        '{accountId}' => $parameters["a"],
                                        '{event}' => 'visitor property:' . json_encode($payload["d"]["visitor"]["props"]),
                                        '{endPoint}' => CommonUtil::getEventsUrl()
                                    ]
                                );
                            } else {
                                LoggerService::log(
                                    Logger::INFO,
                                    'IMPRESSION_SUCCESS',
                                    [
                                        '{mainKeys}' => json_encode(["campaignId" => $campaign['id'], "variationId" => $bucketInfo['id']]),
                                        '{endPoint}' => Urls::TRACK_USER_ENDPOINT,
                                        '{accountId}' => $this->settings['accountId']
                                    ],
                                    self::CLASSNAME
                                );
                            }
                        }
                    } else {
                        LoggerService::log(
                            Logger::INFO,
                            'CAMPAIGN_USER_ALREADY_TRACKED',
                            ['{userId}' => $userId, '{campaignKey}' => $campaignKey, '{api}' => self::$apiName],
                            self::CLASSNAME
                        );
                    }
                }

                return $bucketInfo['name'];
            }
        } catch (Exception $e) {
            LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
        }
        return null;
    }

    /**
     * Gets the variation assigned for the user for the campaign
     *
     * @param  string $campaignKey
     * @param  string $userId
     * @param  array  $options
     * @return string|null
     */
    public function getVariationName($campaignKey, $userId, $options = [])
    {
        self::$apiName = 'getVariationName';
        LoggerService::setApiName(self::$apiName);

        if ($this->isOptedOut()) {
            return false;
        }
        return $this->getVariation($campaignKey, $userId, $options, 0, self::$apiName);
    }

    /**
     * @param  $tagKey
     * @param  $tagValue
     * @param  $userId
     * @return bool
     */
    public function push($tagKey, $tagValue, $userId = '')
    {
        self::$apiName = 'push';
        LoggerService::setApiName(self::$apiName);

        if ($this->isOptedOut()) {
            return false;
        }

        $customDimensionMap = [];
        //reshuffling
        if (!$userId || is_array($tagKey)) {
            $customDimensionMap = $tagKey;
            $userId = $tagValue;
        } else {
            $customDimensionMap[$tagKey] = $tagValue;
        }

        try {
            if (!ValidationsUtil::pushApiParams($userId, $customDimensionMap)) {
                LoggerService::log(Logger::ERROR, 'API_BAD_PARAMETERS', ['{api}' => self::$apiName], self::CLASSNAME);
                return false;
            }

            if ($this->isEventArchEnabled()) {
                $parameters = ImpressionBuilder::getEventsBaseProperties($this->settings['accountId'], $this->getSDKKey(), EventEnum::VWO_SYNC_VISITOR_PROP);
                $payload = ImpressionBuilder::getPushPayloadData(
                    $this->settings,
                    $userId,
                    EventEnum::VWO_SYNC_VISITOR_PROP,
                    $customDimensionMap
                );
                $result = $this->eventDispatcher->sendPost($parameters, $payload);
            } else {
                foreach ($customDimensionMap as $tagKey => $tagValue) {
                    $parameters = ImpressionBuilder::getPushQueryParams($this->settings['accountId'], $userId, $this->getSDKKey(), $tagKey, $tagValue);
                    LoggerService::log(
                        Logger::DEBUG,
                        'IMPRESSION_FOR_PUSH',
                        ['{properties}' => $this->getAllowedToLogImpressionParams($parameters)],
                        self::CLASSNAME
                    );
                    $this->eventDispatcher->sendAsyncRequest(CommonUtil::getUrl(Urls::PUSH_ENDPOINT), 'GET', $parameters);
                    if (!$this->isDevelopmentMode) {
                        LoggerService::log(
                            Logger::INFO,
                            'IMPRESSION_SUCCESS',
                            [
                                '{endPoint}' => Urls::PUSH_ENDPOINT,
                                '{accountId}' => $this->settings['accountId'],
                                '{mainKeys}' => json_encode(["tags" => $parameters['tags']])
                            ],
                            self::CLASSNAME
                        );
                        $result = true;
                    }
                }
            }

            if ($this->isDevelopmentMode) {
                return true;
            } elseif ($result) {
                return $result;
            }
            LoggerService::log(Logger::ERROR, 'IMPRESSION_FAILED', ['{endPoint}' => 'push', '{err}' => ''], self::CLASSNAME);
        } catch (Exception $e) {
            LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
        }

        return false;
    }

    public function getSDKKey()
    {
        $sdkKey = '';
        if (isset($this->settings["sdkKey"])) {
            $sdkKey = $this->settings["sdkKey"];
        }
        return $sdkKey;
    }

    private function isEligibleToSendImpressionToVWO()
    {
        return (
            empty($this->_userStorageObj) ||
            !$this->variationDecider->hasStoredVariation
        );
    }

    private function getGoalTypeToTrack($options)
    {
        $goalTypeToTrack = null;
        if (!isset($options['goalTypeToTrack'])) {
            if ($this->goalTypeToTrack) {
                $goalTypeToTrack = $this->goalTypeToTrack;
            } else {
                $goalTypeToTrack = self::GOAL_TYPES['ALL'];
            }
        } elseif (array_key_exists($options['goalTypeToTrack'], self::GOAL_TYPES)) {
            $goalTypeToTrack = $options['goalTypeToTrack'];
        } else {
            LoggerService::log(
                Logger::ERROR,
                'CONFIG_PARAMETER_INVALID',
                ['{parameter}' => 'goalTypeToTrack', '{api}' => self::$apiName, '{type}' => 'strings(REVENUE, CUSTOM, ALL)'],
                self::CLASSNAME
            );
        }
        return $goalTypeToTrack;
    }

    private function getAllowedToLogImpressionParams($parameters)
    {
        unset($parameters['env']);
        return json_encode($parameters);
    }

    /**
     * Manually opting out of VWO SDK, No tracking will happen
     *
     * @return bool
     */
    public function setOptOut()
    {
        self::$apiName = 'optOut';
        LoggerService::setApiName(self::$apiName);

        LoggerService::log(
            Logger::INFO,
            'OPT_OUT_API_CALLED',
            [],
            self::CLASSNAME
        );

        $this->isOptedOut = true;
        $this->settings = null;
        $this->_userStorageObj = null;
        $this->eventDispatcher = null;
        $this->variationDecider = null;
        return $this->isOptedOut;
    }

    /**
     * Check if VWO SDK is manually opted out
     *
     * @return bool
     */
    private function isOptedOut()
    {
        if ($this->isOptedOut) {
            LoggerService::log(
                Logger::INFO,
                'API_NOT_ENABLED',
                ['{api}' => self::$apiName],
                self::CLASSNAME
            );
        }
        return $this->isOptedOut;
    }

    private function isEventArchEnabled()
    {
        return isset($this->settings['isEventArchEnabled']) && $this->settings['isEventArchEnabled'];
    }
}
