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

class Settings9
{
    public static function setup()
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
                ],
                [
                    'goals' => [
                        [
                            'identifier' => 'REVENUE',
                            'id' => 1,
                            'type' => 'REVENUE_TRACKING'
                        ],
                        [
                            'identifier' => 'CUSTOM',
                            'id' => 214,
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
                    'id' => 231,
                    'percentTraffic' => 100,
                    'key' => 'DEV_TEST_2',
                    'status' => 'RUNNING',
                    'type' => 'VISUAL_AB'
                ],
                [
                    'goals' => [
                        [
                            'identifier' => 'CUSTOM',
                            'id' => 215,
                            'type' => 'CUSTOM_GOAL'
                        ]
                    ],
                    'variations' => [
                        [
                            'id' => 1,
                            'name' => 'Control',
                            'changes' => [],
                            'weight' => 20
                        ],
                        [
                            'id' => 2,
                            'name' => 'Variation-1',
                            'changes' => [],
                            'weight' => 80
                        ]
                    ],
                    'id' => 232,
                    'percentTraffic' => 100,
                    'key' => 'DEV_TEST_3',
                    'status' => 'RUNNING',
                    'type' => 'VISUAL_AB'
                ],
                [
                    'goals' => [
                        [
                            'identifier' => 'CUSTOM',
                            'id' => 216,
                            'type' => 'CUSTOM_GOAL'
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
                    'percentTraffic' => 20,
                    'key' => 'DEV_TEST_4',
                    'status' => 'RUNNING',
                    'type' => 'VISUAL_AB'
                ],
                [
                    'goals' => [
                        [
                            'identifier' => 'CUSTOM',
                            'id' => 217,
                            'type' => 'CUSTOM_GOAL'
                        ]
                    ],
                    'variations' => [
                        [
                            'id' => 1,
                            'name' => 'Control',
                            'changes' => [],
                            'weight' => 0
                        ],
                        [
                            'id' => 2,
                            'name' => 'Variation-1',
                            'changes' => [],
                            'weight' => 100
                        ]
                    ],
                    'id' => 234,
                    'percentTraffic' => 100,
                    'key' => 'DEV_TEST_5',
                    'status' => 'RUNNING',
                    'type' => 'VISUAL_AB'
                ],
                [
                    'goals' => [
                        [
                            'identifier' => 'CUSTOM',
                            'id' => 218,
                            'type' => 'CUSTOM_GOAL'
                        ]
                    ],
                    'variations' => [
                        [
                            'id' => 1,
                            'name' => 'Control',
                            'changes' => [],
                            'weight' => 33.3333
                        ],
                        [
                            'id' => 2,
                            'name' => 'Variation-1',
                            'changes' => [],
                            'weight' => 33.3333
                        ],
                        [
                            'id' => 3,
                            'name' => 'Variation-2',
                            'changes' => [],
                            'weight' => 33.3333
                        ]
                    ],
                    'id' => 235,
                    'percentTraffic' => 100,
                    'key' => 'DEV_TEST_6',
                    'status' => 'RUNNING',
                    'type' => 'VISUAL_AB'
                ]
            ],
            'accountId' => 123456,
            'version' => 1
        ];
    }
}
