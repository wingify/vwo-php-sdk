<?php

/**
 * Copyright 2019-2020 Wingify Software Pvt. Ltd.
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
     * @param  $level
     * @param  $message
     * @param array $params
     * @param string $classname
     * @return bool
     */
    public static function log($level, $message, $params = [], $classname = '')
    {
        if (self::$_logger == null) {
            self::$_logger = new VWOLogger(Logger::DEBUG, 'php://stdout');
        }
        if (empty($classname)) {
            $classname = self::name();
        }
        $message = CommonUtil::buildLogMessage($message, $params, $classname, self::$apiName);
        self::$_logger->log($message, $level);

        return false;
    }
}
