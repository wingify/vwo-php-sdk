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

use vwo\VwoSdkLogMessages;

class LogMessagesUtil
{
    protected static $instance;

    private $infoLogs;
    private $debugLogs;
    private $errorLogs;
    private $warnLogs;

    private function __construct()
    {
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new LogMessagesUtil();
            self::$instance->setLogs();
        }
        return self::$instance;
    }

    /**
     * get all the log messages from vwo-sdk-log-messages repo and make them available to use
     */
    private function setLogs()
    {
        $logs = VwoSdkLogMessages::get();
        $this->infoLogs = $logs["infoLogs"];
        $this->debugLogs = $logs["debugLogs"];
        $this->errorLogs = $logs["errorLogs"];
        $this->warnLogs = $logs["warnLogs"];
    }

    /**
     * get info log messages present for passed type
     *
     * @param  string $type
     * @return string
     */
    public function getInfoMessage($type)
    {
        if (!array_key_exists($type, $this->infoLogs)) {
            return $type;
        }
        return $this->infoLogs[$type];
    }

    /**
     * get debug log messages present for passed type
     *
     * @param  string $type
     * @return string
     */
    public function getDebugMessage($type)
    {
        if (!array_key_exists($type, $this->debugLogs)) {
            return $type;
        }
        return $this->debugLogs[$type];
    }

    /**
     * get error log messages present for passed type
     *
     * @param  string $type
     * @return string
     */
    public function getErrorMessage($type)
    {
        if (!array_key_exists($type, $this->errorLogs)) {
            return $type;
        }
        return $this->errorLogs[$type];
    }

    /**
     * get warning log messages present for passed type
     *
     * @param  string $type
     * @return string
     */
    public function getWarnMessage($type)
    {
        if (!array_key_exists($type, $this->warnLogs)) {
            return $type;
        }
        return $this->warnLogs[$type];
    }
}
