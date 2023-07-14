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

class SettingsFileEventProperties
{
    public static function setup()
    {
        return [
            'sdkKey' => 'loremipsum1234567',
            'campaigns' => [
            [
                'goals' => [
                    [
                        'identifier' => 'Track1',
                        'id' => 216,
                        'type' => 'CUSTOM_GOAL'
                    ],
                    [
                        'identifier' => 'Track2',
                        'id' => 217,
                        'type' => 'CUSTOM_GOAL'
                    ],
                    [
                        'identifier' => 'Track3',
                        'id' => 218,
                        'type' => 'REVENUE_TRACKING',
                        'revenueProp' => 'abcd'
                    ],
                    [
                        'identifier' => 'Track4',
                        'id' => 219,
                        'type' => 'REVENUE_TRACKING',
                        'mca' => -1
                    ]
                ],
                'variations' => [
                    [
                    'id' => 1,
                    'name' => 'Control',
                    'changes' => [],
                    'weight' => 10
                    ],
                    [
                        'id' => 2,
                    'name' => 'Variation-1',
                    'changes' => [],
                    'weight' => 90
                    ]
                ],
                'id' => 233,
                'name' => "Track",
                'percentTraffic' => 100,
                'key' => 'Track',
                'status' => 'RUNNING',
                'type' => 'VISUAL_AB',
                'isForcedVariationEnabled'=> false
            ]
            ],
            'accountId' => 123456,
            'version' => 1,
            'isEventArchEnabled' => true
        ];
    }
}
