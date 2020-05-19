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

namespace vwo\Utils;

use Monolog\Logger;
use vwo\Core\Bucketer;
use vwo\VWO as VWO;

/***
 * All the common function will be invoked from common  class
 *
 * Class Common
 *
 * @package vwo\Utils
 */
class Campaign
{
    /**
     * to set the range on the setting level means to each and every campaign
     * @param  array $settings
     * @return array
     * @throws \Exception
     */
    public static function makeRanges($settings = [])
    {
        if (isset($settings['campaigns']) && count($settings['campaigns'])) {
            foreach ($settings['campaigns'] as $key => $campaign) {
                $settings['campaigns'][$key]['variations'] = Bucketer::addRangesToVariations($campaign['variations']);
            }
        }

        return $settings;
    }
}
