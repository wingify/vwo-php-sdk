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

use Monolog\Logger as Logger;
use vwo\Constants\FileNameEnum;
use vwo\Constants\Urls;
use vwo\Services\LoggerService;
use vwo\HttpHandler\Connection as Connection;
use vwo\Constants\Visitor as VisitorConstants;
use vwo\Constants\HttpRetries;
use Exception;
class EventDispatcher
{
    static $isDevelopmentMode;
    const CLASSNAME = FileNameEnum::EVENT_DISPATCHER;

    function __construct($isDevelopmentMode)
    {
        self::$isDevelopmentMode = $isDevelopmentMode;

        return $this;
    }

    /**
     * API to track data
     *
     * @param string url
     * @param array      $parameters
     *
     * @return boolean
     */
    public function send($url, $parameters, $timeout)
    {
        $attempt = 0;

        if (self::$isDevelopmentMode) {
            return false;
        }

        while ($attempt < HttpRetries::MAX_RETRIES) {
            try {
                $connection = new Connection();
                $response = $connection->get($url, $parameters, $timeout);

                if ($response !== false && isset($response['httpStatus']) && $response['httpStatus'] == 200) {
                    return $response; // Successful response, stop retrying
                }
                $error = "Received status code " . $response['httpStatus'];
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            $attempt++;
            LoggerService::logWithMessage(Logger::WARNING, "Failed to get settingsFile. Retrying in " . pow(2, $attempt) . " seconds. Attempt: " . $attempt . " of " . HttpRetries::MAX_RETRIES, [], self::CLASSNAME);
            sleep(pow(2, $attempt));
        }

        // Log failure after exhausting retries
        LoggerService::log(
            Logger::ERROR, 'IMPRESSION_FAILED', [
            '{endPoint}' => $url,
            '{err}' => 'Request failed after max retries: ' . $error
            ], self::CLASSNAME
        );

        return false;
    }

    /**
     * Send async call to the destination i.e. VWO server
     *
     * @param string $url
     * @param string $method
     * @param array  $params
     *
     * @return int|false
     */
    public function sendAsyncRequest($url, $method, $params = [], $body = [], $sdkKey = '')
    {
        $attempt = 0;

        while ($attempt < HttpRetries::MAX_RETRIES) {

            // If in DEV mode, do not send any call
            if (self::$isDevelopmentMode) {
                return false;
            }

            // Parse url and extract information
            $parsedUrl = parse_url($url);
            $host = $parsedUrl['host'];
            $path = $parsedUrl['path'];

            // Open socket connection
            $socketConnection = fsockopen('ssl://' . $host, 443, $errno, $errstr, 60);
            if (!$socketConnection) {
                LoggerService::log(
                    Logger::ERROR,
                    'IMPRESSION_FAILED',
                    [
                        '{endPoint}' => $url,
                        '{err}' => 'Unable to connect to ' . $host . '. Error: ' . $errstr . ' ' . ($errno)
                    ],
                    self::CLASSNAME
                );

                $attempt++;
                LoggerService::logWithMessage(Logger::WARNING, "Failed to send impression request. Retrying in " . pow(2, $attempt) . " seconds. Attempt: " . $attempt . " of " . HttpRetries::MAX_RETRIES, [], self::CLASSNAME);
                sleep(1 * pow(2, $attempt));
                continue;
            }

            // Build request
            $request  = $method . ' ' . $path . '?' . http_build_query($params);
            $request .= ' HTTP/1.1' . "\r\n";
            $request .= 'Host: ' . $host . "\r\n";
            $request .= VisitorConstants::CUSTOM_HEADER_USER_AGENT . ': ' . $params[VisitorConstants::USER_AGENT] . "\r\n";
            $request .= VisitorConstants::CUSTOM_HEADER_IP . ': ' . $params[VisitorConstants::IP] . "\r\n";

            // If POST request and body is set, add Content-Type and Content-Length headers
            if ($method === 'POST' && isset($body)) {
                $request .= 'Content-Type: application/json' . "\r\n";  // Assuming JSON body
                $request .= 'Content-Length: ' . strlen($body) . "\r\n"; // Ensure Content-Length is correct

                // Add Authorization header if sdkKey is provided and not empty
                if ($sdkKey && !empty($sdkKey)) {
                    $request .= 'Authorization: ' . $sdkKey . "\r\n";
                }
            }

            $request .= 'Connection: Close' . "\r\n\r\n";

            // Append body for POST requests
            if ($method === 'POST' && isset($body)) {
                $request .= $body;  // Add the JSON body content
            }

            // Send Request
            fwrite($socketConnection, $request);
            // Read the response
            $response = '';
            while (!feof($socketConnection)) {
                $response .= fgets($socketConnection, 1024);
            }
            fclose($socketConnection);

            // Extract HTTP status code
            preg_match('/HTTP\/\d\.\d\s+(\d+)\s+/', $response, $matches);
            $statusCode = isset($matches[1]) ? (int)$matches[1] : 0;

            // Retry if status code is 400 or 500
            if ($statusCode >= 200 && $statusCode < 300) {
                return true; // Success, exit retry loop
            } else {
                $attempt++;
                LoggerService::logWithMessage(Logger::WARNING, "Received status $statusCode. Retrying in " . pow(2, $attempt) . " seconds. Attempt: " . $attempt . " of " . HttpRetries::MAX_RETRIES, [], self::CLASSNAME);
                sleep(1 * pow(2, $attempt));
                continue;
            }
        }
        LoggerService::log(Logger::ERROR, 'IMPRESSION_FAILED', ['{endPoint}' => $url, '{err}' => 'Received status code ' . $statusCode . ' after max retries'], self::CLASSNAME);
        return false;
    }

    /**
     * Send call to the destination i.e. VWO server
     *
     * @param  array $params
     * @param  array $postData
     * @return bool
     */
    public function sendEventRequest($params = [], $postData = [])
    {
        if (self::$isDevelopmentMode) {
            return false;
        }

        // Construct the URL
        $url = Common::getEventsUrl();

        // If POST data exists, use POST method and include the body
        $method = !empty($postData) ? 'POST' : 'GET';
        $body = !empty($postData) ? json_encode($postData) : null;

        return $this->sendAsyncRequest($url, $method, $params, $body);
    }

    /**
     * Send batch events call to the destination i.e. VWO server
     *
     * @param  string $sdkKey
     * @param  array  $params
     * @param  array  $postData
     * @return bool
     */
    public function sendBatchEventRequest($sdkKey, $params, $postData)
    {
        if (self::$isDevelopmentMode) {
            return false;
        }

        // construct the url
        $url = Common::getBatchEventsUrl();

        // If POST data exists, use POST method and include the body
        $method = !empty($postData) ? 'POST' : 'GET';
        $body = !empty($postData) ? json_encode($postData) : null;

        $result = $this->sendAsyncRequest($url, $method, $params, $body, $sdkKey);
        if ($result) {
            LoggerService::log(
                Logger::INFO,
                'IMPRESSION_BATCH_SUCCESS',
                [
                    '{endPoint}' => $url,
                    '{accountId}' => $params['a']
                ],
                self::CLASSNAME
            );
        }
        return $result;
    }
}