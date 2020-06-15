<?php

/**
 * Copyright 2019-2020 Wingify Software Pvt. Ltd.
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

class Settings1
{
    public static function use()
    {
        return [
            'sdkKey' => 'loremipsum123456',
            'campaigns' => [
            [
                'goals' => [
                [
                    'identifier' => 'CUSTOM',
                  'id' => 213,
                  'type' => 'CUSTOM_GOAL'
                ]
                ],
                'variations' => [
                [
                    'id' => 1,
                  'name' => 'Control',
                  'changes' => [],
                  'weight' => 50
                ],
                [
                    'id' => 2,
                  'name' => 'Variation-1',
                  'changes' => [],
                  'weight' => 50
                ]
                ],
                'id' => 230,
                'percentTraffic' => 50,
                'key' => 'DEV_TEST_1',
                'status' => 'RUNNING',
                'type' => 'VISUAL_AB'
            ]
            ],
            'accountId' => 123456,
            'version' => 1
        ];
    }
}
