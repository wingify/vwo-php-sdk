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

namespace vwo\Constants;

class Urls
{
    /**
     * base url for api hit
     */
    const BASE_URL = 'https://dev.visualwebsiteoptimizer.com/server-side/';
    /**
     * query string to fetch settings
     */
    const SETTINGS_URL = self::BASE_URL . 'settings';
    /**
     * query string to fetch settings once webhook is triggered
     */
    const WEBHOOK_SETTINGS_URL = self::BASE_URL . 'pull';
    /**
     * query string to track goal
     */
    const TRACK_GOAL_URL = self::BASE_URL . 'track-goal';
    /**
     * query string to track user
     */
    const TRACK_USER_URL = self::BASE_URL . 'track-user';
    /**
     * query string to push api
     */
    const PUSH_URL = self::BASE_URL . 'push';
}
