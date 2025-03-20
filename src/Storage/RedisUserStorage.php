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

namespace vwo\Storage;

use Exception as Exception;
use Predis\Client;
use Monolog\Logger as Logger;
use vwo\Services\LoggerService as LoggerService;

/**
 * Interface UserStorageInterface
 *
 * user interface should be included and used to set and get user-campaign mapping
 */
class RedisUserStorage implements UserStorageInterface
{
    const CLASSNAME = 'vwo/Storage/RedisUserStorage';
    private $redisConnection;
    private $redisDatabase;


    /**
     * Constructor for initializing the RedisUserStorageService.
     *
     * @param  RedisConfig $redisConfig Configuration object containing Redis server details.
     * @throws Exception If there is an issue connecting to Redis.
     */
    public function __construct($redisConfig)
    {
        try {
            $options = parse_url($redisConfig['url']);

            // Initialize Redis connection here
            // Connect to the Redis server using the host and port from the URL
            $this->redisConnection = new Client(['host' => $options['host'], 'port' => $options['port'], 'password'=> $redisConfig['password']]);

            // Assign the Redis database connection
            $this->redisDatabase = $this->redisConnection;
        } catch (Exception $ex) {
            // Handle the exception, e.g., log it or take appropriate action
            LoggerService::log(Logger::ERROR, 'Error connecting to Redis ', [], self::CLASSNAME);
            throw $ex;
        }
    }


    /**
     * Get user-campaign mapping for the given user ID and campaign key.
     *
     * @param  string $userId      User ID.
     * @param  string $campaignKey Campaign key.
     * @return array|null The user-campaign mapping as an associative array, or null if not found.
     */
    public function Get($userId, $campaignKey)
    {
        if ($this->redisDatabase !== null) {
            try {
                $key = $campaignKey . ':' . $userId;
                $result = $this->redisDatabase->get($key);

                if (!empty($result)) {
                    return json_decode($result, true); // Pass true to decode as an associative array
                }
            } catch (Exception $ex) {
                LoggerService::log(Logger::ERROR, 'Error while getting data from Redis ', ['{userId}' => $userId], self::CLASSNAME);
            }
        } else {
            LoggerService::log(Logger::ERROR, 'Redis client not initialized ', ['{userId}' => $userId], self::CLASSNAME);
        }

        return null;
    }
    /**
     * @param  $campaignInfo
     * @return array
     */

    /**
     * Set user-campaign mapping using the provided user storage map.
     *
     * @param $campaignInfo User storage map to be stored.
     */
    public function Set($campaignInfo)
    {
        if ($this->redisDatabase !== null) {
            $key = $campaignInfo['campaignKey'] . ':' . $campaignInfo['userId'];
            $data = json_encode($campaignInfo);

            try {
                $this->redisDatabase->set($key, $data);
            } catch (Exception $ex) {
                LoggerService::log(Logger::ERROR, 'Error while setting data in Redis ', ['{userId}' => $campaignInfo['userId']], self::CLASSNAME);
                throw $ex;
            }
        } else {
            LoggerService::log(Logger::ERROR, 'Redis client not initialized ', ['{userId}' => $campaignInfo['userId']], self::CLASSNAME);
        }
    }
}
