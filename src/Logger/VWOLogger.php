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

namespace vwo\Logger;

use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/***
 * Class VWOLogger
 * By default monolog is implemented for logging
 *
 * @package vwo\Logger
 */
class VWOLogger implements LoggerInterface
{
    var $logger;

    /***
     * DefaultLogger constructor.
     *
     * used to initialize the monolog with line formatter
     *
     * @param int $minLevel
     * @param string $stream
     * @param string $settings
     */
    /*
     * The function __construct has references from:
     *
     * 1. “Seldaek/monolog” by Jordi Boggiano
     *      Copyright (c) 2011-2019 Jordi Boggiano under MIT License
     *      Source - https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/LineFormatter.php
     *
     * 2. “Optimizely PHP SDK” by Optimizely
     *      Copyright 2016-2019, Optimizely,used under Apache 2.0 License.
     *      Source - https://github.com/optimizely/php-sdk/blob/master/src/Optimizely/Logger/DefaultLogger.php
     *
     * 3. StackOverflow by Markus (https://stackoverflow.com/users/3609511/markus)
     *      Source - https://stackoverflow.com/questions/26480611/how-to-set-up-a-generic-leveraged-logging-using-monolog
     */
    public function __construct($minLevel = Logger::INFO, $stream = "")
    {
        $streamHandler = $this->getStreamHandler($stream, $minLevel);
        $lineformatter = $this->getFormatter();
        $streamHandler->setFormatter($lineformatter);
        $this->logger = new Logger('VWO-SDK');
        $this->logger->pushHandler($streamHandler);
    }

    /**
     * @param $stream
     * @param $minLevel
     */
    private function getStreamHandler($stream, $minLevel)
    {
        if (!empty($stream)) {
            $streamHandler = new StreamHandler($stream, $minLevel);
        } else {
            $streamHandler = new StreamHandler("php://stdout", $minLevel);
        }
        return $streamHandler;
    }

    /**
     * @return LineFormatter
     */
    private function getFormatter()
    {
        return new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n");
    }

    /***
     * to add logs to monolog
     *
     * @param  $msg
     * @param  int $level
     * @return mixed|void
     */
    public function log($msg, $level = Logger::INFO)
    {
        $x = $this->logger->addRecord($level, $msg);
    }
}
