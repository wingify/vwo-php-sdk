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

use Monolog\Logger as Logger;
use vwo\Services\LoggerService;
use vwo\Constants\LogMessages as LogMessages;
use vwo\HttpHandler\Connection as Connection;

class EventDispatcher
{
    static $isDevelopmentMode;

    function __construct($isDevelopmentMode)
    {
        self::$isDevelopmentMode = $isDevelopmentMode;

        return $this;
    }

    /***
     * API to track visitor hit to vwo
     *
     * @param array $campaign
     * @param string $userId
     * @return boolean
     */
    public function send($url, $parameters)
    {

        if (self::$isDevelopmentMode) {
            return false;
        } else {
            $connection = new Connection();

            $response = $connection->get($url, $parameters);
        }

        if (isset($response['httpStatus']) && $response['httpStatus'] == 200) {
            return $response;
        } else {
            return false;
        }
        LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['IMPRESSION_FAILED'], ['{endPoint}' => $url]);

        return false;
    }
}
