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

namespace vwo\Constants;

class Urls
{
    /**
     * base url for api hit
     */
    const BASE_URL = 'https://dev.visualwebsiteoptimizer.com/';

    const SERVER_SIDE_URI = 'server-side/';
    /**
     * query string to fetch settings
     */
    const SETTINGS_URL = self::BASE_URL . self:: SERVER_SIDE_URI . 'settings';
    /**
     * query string to fetch settings once webhook is triggered
     */
    const WEBHOOK_SETTINGS_URL = self::BASE_URL . self:: SERVER_SIDE_URI . 'pull';
    /**
     * query string to track goal
     */
    const TRACK_GOAL_ENDPOINT = 'track-goal';
    /**
     * query string to track user
     */
    const TRACK_USER_ENDPOINT = 'track-user';
    /**
     * query string to push api
     */
    const PUSH_ENDPOINT = 'push';
    /**
     * query string for event arch for track goal, track user and push APIs
     */
    const EVENTS_ENDPOINT = 'events/t';

    /**
     * query string for batch events for track goal and push APIs
     * using when tracking multiple goals or pushing multiple dimensions
     */
    const BATCH_EVENTS_ENDPOINT = 'batch-events';
}
