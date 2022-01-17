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

use Monolog\Logger;
use Exception as Exception;
use Ramsey\Uuid\Uuid;
use vwo\Constants\FileNameEnum;
use vwo\Services\LoggerService;

class UuidUtil
{
    const UUID_SEED = 'https://vwo.com';
    const CLASSNAME = FileNameEnum::UUID_UTIL;

    /**
     * @param  string $userId
     * @param  int    $accountId
     * @param  bool   $disableLogs
     * @return string
     */
    public static function get($userId, $accountId, $disableLogs = false)
    {
        $uuid = '';
        try {
            $uuid5_seed = Uuid::uuid5(Uuid::NAMESPACE_URL, self::UUID_SEED);
            $uuid5_seed_accountId = Uuid::uuid5($uuid5_seed, $accountId);
            $uuid5 = Uuid::uuid5($uuid5_seed_accountId, $userId);
            $uuid = strtoupper(str_replace('-', '', $uuid5->toString()));
            LoggerService::log(Logger::DEBUG, 'USER_UUID', ['{userId}' => $userId, '{accountId}' => $accountId, '{uuid}' => $uuid], self::CLASSNAME, $disableLogs);
        } catch (Exception $e) {
            LoggerService::log(Logger::ERROR, $e->getMessage(), [], self::CLASSNAME);
        }

        return $uuid;
    }
}
