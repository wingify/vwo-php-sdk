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

namespace vwo\Constants;

/***
 * Class Constants
 * All the constant variables are used from constant class
 *
 * @package vwo\Constants
 */
class Constants
{
    /**
     * sdk version for api hit
     */
    const SDK_VERSION = '1.6.0';
    /**
     * sdk langauge for api hit
     */
    const SDK_LANGUAGE = 'php';
    /**
     * base url for api hit
     */
    const BASE_URL = 'https://dev.visualwebsiteoptimizer.com/server-side/';
    /**
     * query string to fetch settings
     */
    const SETTINGS_URL = self::BASE_URL . 'settings';
    /**
     * query string to track goal
     */
    const GOAL_URL = self::BASE_URL . 'track-goal';
    /**
     * query string to track user
     */
    const TRACK_URL = self::BASE_URL . 'track-user';
    /**
     * query string to push api
     */
    const PUSH_URL = self::BASE_URL . 'push';
    /**
     * base seed for uuid
     */
    const UUID_SEED = 'https://vwo.com';



    /**
     * messages used for dedug mode
     */
    const DEBUG_MESSAGES = [
        'LOG_LEVEL_SET' => '({file}): Log level set to {level}',
        'SET_DEVELOPMENT_MODE' => '({file}): DEVELOPMENT mode is {devmode}',
        'VALID_CONFIGURATION' => '({file}): SDK configuration and account settings are valid.',
        'CUSTOM_LOGGER_USED' => '({file}): Custom logger used',
        'SDK_INITIALIZED' => '({file}): SDK properly initialzed',
        'SETTINGS_FILE_PROCESSED' => '({file}): Settings file processed',
        'NO_STORED_VARIATION' => '({file}): No stored variation for UserId:{userId} for Campaign:{campaignTestKey} found in UserStorageService',
        'NO_USER_STORAGE_SERVICE_GET' => '({file}): No UserStorageService to get for stored data',
        'NO_USER_STORAGE_SERVICE_SET' => '({file}): No UserStorageService to set data',
        'GETTING_STORED_VARIATION' => '({file}): Got stored variation for UserId:{userId} of Campaign:{campaignTestKey} as Variation: {variationName}, found in UserStorageService',
        'CHECK_USER_ELIGIBILITY_FOR_CAMPAIGN' => '({file}): campaign:{campaignTestKey} having traffic allocation:{trafficAllocation} assigned value:{trafficAllocation} to userId:{userId}',
        'USER_HASH_BUCKET_VALUE' => '({file}): userId:{userId} having hash:{hashValue} got bucketValue:{bucketValue}',
        'VARIATION_HASH_BUCKET_VALUE' => '({file}): userId:{userId} for campaign:{campaignTestKey} having percent traffic:{percentTraffic} got hash-value:{hashValue} and bucket value:{bucketValue}',
        'GOT_VARIATION_FOR_USER' => '({file}): userId:{userId} for campaign:{campaignTestKey} got variationName:{variationName} inside method:{method}',
        'USER_NOT_PART_OF_CAMPAIGN' => '({file}): userId:{userId} for campaign:{campaignTestKey} did not become part of campaign, method:{method}',
        'UUID_FOR_USER' => '({file}): Uuid generated for userId:{userid} and accountId:{accountId} is {desiredUuid}',
        'FEATURE_FLAG_NOT_LINKED' => '({file}): Feature:{featureKey} is not linked to any running campaigns',
        'USER_HASH_BUCKET_VALUE' => '({file}): User ID:{userId} having hash:{hashValue} got bucketValue:{bucketValue}',
        'VARIATION_HASH_BUCKET_VALUE' => '({file}): User ID:{userId} for campaign:{campaignKey} having percent traffic:{percentTraffic} got bucket value:{bucketValue}',
    ];

    /**
     * messages used for info mode
     */

    const INFO_MESSAGES = [
        'VARIATION_RANGE_ALLOCATION' => '({file}): Campaign:{campaignTestKey} having variations:{variationName} with weight:{variationWeight} got range as: ( {start} - {end} ))',
        'VARIATION_ALLOCATED' => '({file}): UserId:{userId} of Campaign:{campaignTestKey} got variation: {variationName}',
        'GETTING_UP_USER_STORAGE_SERVICE' => '({file}): Getting data into UserStorageService for userId:{userId} successful',
        'SETTING_DATA_USER_STORAGE_SERVICE' => '({file}): Setting data into UserStorageService for userId:{userId} successful',
        'GOT_STORED_VARIATION' => '({file}): Got stored variation:{variationName} of campaign:{campaignTestKey} for userId:{userId} from UserStorageService',
        'NO_VARIATION_ALLOCATED' => '({file}): UserId:{userId} of Campaign:{campaignTestKey} did not get any variation',
        'USER_ELIGIBILITY_FOR_CAMPAIGN' => '({file}): Is userId:{userId} part of campaign? {isUserPart}',
        'AUDIENCE_CONDITION_NOT_MET' => '({file}): userId:{userId} does not become part of campaign because of not meeting audience conditions',
        'GOT_VARIATION_FOR_USER' => '({file}): userId:{userId} for campaign:{campaignTestKey} got variationName:{variationName}',
        'USER_GOT_NO_VARIATION' => '({file}): userId:{userId} for campaign:{campaignTestKey} did not allot any variation',
        'IMPRESSION_SUCCESS' => '({file}): Event sent to VWO - {endPoint} having main keys: accountId:{accountId}, userId:{userId}, campaignId:{campaignId} and variationId:{variationId}',
        'IMPRESSION_SUCCESS_PUSH' => '({file}): Event sent to VWO - {endPoint} having main keys: accountId:{accountId}, userId:{userId} and tags:{tags}',
        'INVALID_VARIATION_KEY' => '({file}): Variation was not assigned to userId:{userId} for campaign:{campaignTestKey}',
        'API_CALLED' => '({file}): API: {api} called for userid : {userId}',
        'IMPRESSION_FOR_TRACK_USER' => '({file}): impression built for track-user - {properties}',
        'IMPRESSION_FOR_TRACK_GOAL' => '({file}): impression built for track-goal - {properties}',
        'IMPRESSION_FOR_PUSH' => '({file}): impression built for push-api - {properties}',
        'FEATURE_ENABLED_FOR_USER' => '({file}): Feature having feature-key:{featureKey} for user ID:{userId} is enabled',
        'FEATURE_NOT_ENABLED_FOR_USER' => '({file}): Feature having feature-key:{featureKey} for user ID:{userId} is not enabled',
        'USER_IN_FEATURE_ROLLOUT' => '({file}): User ID:{userId} is in feature rollout:{featureKey}',
        'USER_NOT_IN_FEATURE_ROLLOUT' => '({file}): User ID:{userId} is NOT in feature rollout:{featureKey}',
        'WHITELISTING_ELIGIBILITY_STATUS' => '({file}): User ID:{userId} of campaign:{campaign_key} with variation_targeting_variables:{variation_targeting_variables} {status} whitelisting and hence {variation} variation is assigned',
        'VARIABLE_FOUND' => '({file}): Value for variable:{variableKey} of campaign:{campaignKey} is:{variableValue} for user:{userId}',
        'VARIABLE_NOT_FOUND' => '({file}): Value for variable:{variableKey} of campaign:{campaignKey} is not found for user:{userId}',
        'SEGMENTATION_STATUS' => '({file}): UserId:{userId} of campaign:{campaignKey} with variables:{customVariables} {status} {segmentationType} {variation}',
        'WHITELISTING_SKIPPED' => '({file}): For userId:{userId} of campaign:{campaignKey},{reason} whitelisting was skipped {variation}',
        'SEGMENTATION_SKIPPED' => '({file}): For userId:{userId} of campaign:{campaignKey}, segment was missing, hence skipping segmentation{variation}',
        'SEGMENTATION_STATUS' => '({file}): For userId:{userId} of campaign:{campaignKey} with variables:{customVariables} {status} {segmentationType} {variation}',



    ];
    /**
     * messages used for warning mode
     */
    const WARNING_MESSAGES = [];
    /**
     * messages used for error mode
     */
    const ERROR_MESSAGE = [
        'INVALID_CONFIGURATION' => '({file}): SDK configuration or account settings or both is/are not valid.',
        'SETTINGS_FILE_CORRUPTED' => '({file}): Settings file is corrupted. Please contact VWO Support for help.',
        'ACTIVATE_API_MISSING_PARAMS' => '({file}): "activate" API got bad parameters. It expects campaignTestKey(String) as first and userId(String/Number) as second argument',
        'ACTIVATE_API_CONFIG_CORRUPTED' => '({file}): "activate" API has corrupted configuration',
        'GET_VARIATION_API_MISSING_PARAMS' => '({file}): "getVariation" API got bad parameters. It expects campaignTestKey(String) as first and userId(String/Number) as second argument',
        'GET_VARIATION_API_CONFIG_CORRUPTED' => '({file}): "getVariation" API has corrupted configuration',
        'TRACK_API_MISSING_PARAMS' => '({file}): "track" API got bad parameters. It expects campaignTestKey(String) as first, userId(String/Number) as second and goalIdentifier (string) as third argument. options is revenueValue(Float/Number/String) and is required for revenue goal only.',
        'TRACK_API_CONFIG_CORRUPTED' => '({file}): "track" API has corrupted configuration',
        'TRACK_API_GOAL_NOT_FOUND' => '({file}): Goal not found for campaign:{campaignTestKey} and userId:{userId}',
        'TRACK_API_VARIATION_NOT_FOUND' => '({file}): Variation not found for campaign:{campaignTestKey} and userId:{userId}',
        'CAMPAIGN_NOT_RUNNING' => '({file}): Campaign:{campaignTestKey} is not RUNNING. Please verify from VWO App',
        'GET_USER_STORAGE_SERVICE_FAILED' => '({file}): Getting data from UserStorageService failed for userId:{userId}',
        'SET_USER_STORAGE_SERVICE_FAILED' => '({file}): Setting data into UserStorageService failed for userId:{userId}',
        'INVALID_CAMPAIGN' => '({file}): Invalid campaign passed to {method} of this file',
        'IMPRESSION_FAILED' => '({file}): Event could not be sent to VWO - {endPoint}',
        'USERID_KEY_CORRUPTED' => '({file}): userId parameter value - {userId} is corrupted',
        'FEATURE_KEY_CORRUPTED' => '({file}): featureKey parameter value - {featureKey} is corrupted',
        'CUSTOM_LOGGER_MISCONFIGURED' => '({file}): Custom logger is provided but seems to have misconfigured. Please check the API Docs. Using default logger.',
        'MISSING_GOAL_REVENUE' => 'Revenue value should be passed for revenue goal {goalIdentifier} for campaign {campaignTestKey} and userId {userId}',
        'TAG_KEY_LENGTH_ERROR' => '({file}): Length of tagKey:{tagKey} for userID:{userId} can not be greater than 255',
        'TAG_VALUE_LENGTH_ERROR' => '({file}): Length of tagValue:{tagValue} for userID:{userId} can not be greater than 255',
        'INVALID_USER_ID' => '({file}): Invalid userId:{userId} passed to {method} of this file',
        'TAG_KEY_CORRUPTED' => '({file}): Invalid tagKey:{tagKey} passed to {method} of this file',
        'TAG_VALUE_CORRUPTED' => '({file}): Invalid tagValue:{tagValue} passed to {method} of this file',
        'INVALID_CAMPAIGN_FOR_API' => '({file}): {api} API is not valid for user ID: {userId} in Campaign Key: {campaignKey} having campaign type: {campaignType}',
        'INVALID_API_CALL' => '({file}): {api} API is not valid for user ID: {userId} in Campaign Key: {campaignKey} having campaign type: {campaignType}',
        'ACTIVATE_API_MISSING_PARAMS' => '({file}): "activate" API got bad parameters. It expects campaignKey(String) as first, userId(String) as second and options(optional Object) as third argument',
        'SEGMENTATION_ERROR' => '({file}): Error while segmenting the userId:{userId} of campaignKey:{campaignKey}{variation} with customVariables:{customVariables}. Error message: {err}'
    ];


    const VARIABLE_TYPE_BOOLEAN = 'boolean';
    const VARIABLE_TYPE_INTEGER = 'integer';
    const VARIABLE_TYPE_DOUBLE = 'double';
    const VARIABLE_TYPE_STRING = 'string';
}
