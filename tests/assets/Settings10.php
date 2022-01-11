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

namespace vwo;

class Settings10
{
    public static function setup()
    {
        return array(
            'sdkKey' => 'loremipsum123456',
            'campaigns' =>
                   array(
                    6 =>
                        [
                            'id' => 92,
                            "name" => "Campaign-92",
                            'segments' => [],
                            'status' => 'RUNNING',
                            'percentTraffic' => 0,
                            'type' => 'FEATURE_ROLLOUT',
                            'variables' => [
                                [
                                    'value' => 10,
                                    'type' => 'integer',
                                    'key' => 'V1',
                                    'id' => 1,
                                ],
                                [
                                    'value' => "jdhgf",
                                    'type' => 'json',
                                    'key' => 'V1',
                                    'id' => 1,
                                ]
                            ],
                            'key' => 'FEATURE_ROLLOUT_ZERO_TRAFFIC',
                            'variations' => [
                                [
                                    'id' => 1,
                                    'name' => 'website',
                                    'changes' => [],
                                    'weight' => 100,
                                ],
                            ],
                            'goals' => [
                                [
                                    'identifier' => 'h',
                                    'id' => 209,
                                    'type' => 'CUSTOM_GOAL',
                                ],
                                [
                                    'identifier' => 'jk',
                                    'id' => 210,
                                    'type' => 'CUSTOM_GOAL',
                                ]
                            ],
                        ],
                ),
            'accountId' => 0,
            'version' => 1,
        );
    }
}
