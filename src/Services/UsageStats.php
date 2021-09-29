<?php

/**
 * Copyright 2019-2021 Wingify Software Pvt. Ltd.
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

namespace vwo\Services;

use Monolog\Logger as Logger;
use vwo\Constants\LogMessages as LogMessages;
use vwo\Logger\LoggerInterface;
use vwo\Services\LoggerService as LoggerService;
use vwo\Storage\UserStorageInterface;

class UsageStats
{
    private $usageStats;

    function __construct($stats, $config, $isDevelopmentMode = false)
    {
        $this->usageStats = [];
        if (!$isDevelopmentMode) {
            if (isset($config['integrations'])) {
                $stats['ig'] = 1;
            }
            $this->usageStats = $stats;
            if (count($this->usageStats)) {
                $this->usageStats['_l'] = 1;
            }
        }
    }

    /**
     * Get the collected usage stats.
     *
     * @returns array usage stats data
     */
    public function getUsageStats()
    {
        return $this->usageStats;
    }
};
