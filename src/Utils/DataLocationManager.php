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


use vwo\Constants\Urls;

class DataLocationManager
{
    protected static $instance;

    private $settings;

    private function __construct()
    {
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new DataLocationManager();
        }
        return self::$instance;
    }

    public function getDataLocation()
    {
        $url = Urls::BASE_URL;
        if(isset($this->settings['dataResidencyLocation']) && $this->settings['dataResidencyLocation']) {
            $url = $url . $this->settings['dataResidencyLocation'] . '/';
        }
        return $url;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}
