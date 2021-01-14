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

    /**
     * API to track data
     *
     * @param string url
     * @param array $parameters
     *
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
        LoggerService::log(Logger::ERROR, LogMessages::ERROR_MESSAGES['IMPRESSION_FAILED'], ['{endPoint}' => $url, '{reason}' => '']);

        return false;
    }

    /**
     * Send async call to the destination i.e. VWO server
     *
     * @param string $url
     * @param string $method
     * @param array $params
     *
     * @return void
     */
    public function sendAsyncRequest($url, $method, $params = [])
    {
        // If in DEV mode, do not send any call
        if (self::$isDevelopmentMode) {
            return;
        }

        // Parse url and extract information
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];
        $path = $parsedUrl['path'];

        // Open socket connectio
        $socketConnection = fsockopen('ssl://' . $host, 443, $errno, $errstr, 60);
        if (!$socketConnection) {
            LoggerService::log(
                Logger::ERROR,
                LogMessages::ERROR_MESSAGES['IMPRESSION_FAILED'],
                [
                    '{endPoint}' => $url,
                    '{reason}' => 'Unable to connect to ' . $host . '. Error: ' . $errstr . ' ' . ($errno)]
            );

            return;
        }

        // Build request
        $request  = $method . ' ' . $path . '?' . http_build_query($params);
        $request .= ' HTTP/1.1' . "\r\n";
        $request .= 'Host: ' . $host . "\r\n";
        $request .= 'Connection: Close' . "\r\n\r\n";

        // Send Request
        fwrite($socketConnection, $request);
        fclose($socketConnection);
    }
}
