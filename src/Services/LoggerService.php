<?php

/**
 * Copyright 2019-2022 Wingify Software Pvt. Ltd.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace vwo\Services;

use Exception as Exception;
use Monolog\Logger as Logger;
use vwo\Utils\Common as CommonUtil;
use vwo\Logger\VWOLogger as VWOLogger;
use vwo\Utils\LogMessagesUtil;

class LoggerService
{
    static $_logger;
    static $apiName;

    /**
     * @return string
     */
    static function name()
    {
        return 'vwo\Services\LoggerService';
    }

    public static function setLogger($logger)
    {
        self::$_logger = $logger;
    }

    public static function setApiName($apiName)
    {
        self::$apiName = $apiName;
    }
     /**
      * function to log to the default/ custom logger
      *
      * @param  int    $level
      * @param  string $messageType
      * @param  array  $params
      * @param  string $classname
      * @param  bool   $disableLogs disable logs if True
      * @return bool
      */
    public static function log($level, $messageType, $params = [], $classname = '', $disableLogs = false)
    {
        if (self::$_logger == null) {
            self::$_logger = new VWOLogger(Logger::DEBUG, 'php://stdout');
        }
        if (empty($classname)) {
            $classname = self::name();
        }
        $message = self::getMessageBasedOnLevel($level, $messageType);
        $message = CommonUtil::buildLogMessage($message, $params, $classname, self::$apiName);
        if (!$disableLogs) {
            self::$_logger->log($message, $level);
        }

        return false;
    }

    /**
     * function to log message directly without any message type
     * @param mixed $level log level
     * @param mixed $message log message
     * @param mixed $params log message params
     * @param mixed $classname log message class name
     * @return void
     */
    public static function logWithMessage( $level, $message, $params = [], $classname = '')
    {
        if (self::$_logger == null) {
            self::$_logger = new VWOLogger(Logger::DEBUG, 'php://stdout');
        }
        if (empty($classname)) {
            $classname = self::name();
        }
        $message = CommonUtil::buildLogMessage($message, $params, $classname, self::$apiName);
        self::$_logger->log($message, $level);
    }

    /**
     * function to get log message on the basis of log levels
     *
     * @param  int    $logLevel
     * @param  string $messageType
     * @return string
     */
    private static function getMessageBasedOnLevel($logLevel, $messageType)
    {
        $message = "";
        switch ($logLevel) {
            case Logger::DEBUG:
                $message = LogMessagesUtil::instance()->getDebugMessage($messageType);
                break;
            case Logger::INFO:
                $message = LogMessagesUtil::instance()->getInfoMessage($messageType);
                break;
            case Logger::ERROR:
                $message = LogMessagesUtil::instance()->getErrorMessage($messageType);
                break;
            case Logger::WARNING:
                $message = LogMessagesUtil::instance()->getWarnMessage($messageType);
                break;
        }

        return $message;
    }
}
