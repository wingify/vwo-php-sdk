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

class SettingsFileNewMEG3
{
    public static function setup()
    {
        return [
            'sdkKey' => 'abcde123',
            'campaigns' => [
                [
                    'type' => 'VISUAL_AB',
                    'isForcedVariationEnabled' => false,
                    'goals' => [
                        [
                            'type' => 'CUSTOM_GOAL',
                            'id' => 271,
                            'identifier' => 'CUSTOM'
                        ]
                    ],
                    'percentTraffic' => 100,
                    'id' => 30,
                    'name' => 'test1',
                    'key' => 'test1',
                    'status' => 'RUNNING',
                    'variations' => [
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 1,
                            'name' => 'Control'
                        ],
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 2,
                            'name' => 'Variation-1'
                        ]
                    ]
                ],
                [
                    'type' => 'VISUAL_AB',
                    'isForcedVariationEnabled' => false,
                    'goals' => [
                        [
                            'type' => 'CUSTOM_GOAL',
                            'id' => 271,
                            'identifier' => 'CUSTOM'
                        ]
                    ],
                    'percentTraffic' => 100,
                    'id' => 31,
                    'name' => 'test2',
                    'key' => 'test2',
                    'status' => 'RUNNING',
                    'variations' => [
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 1,
                            'name' => 'Control'
                        ],
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 2,
                            'name' => 'Variation-1'
                        ]
                    ]
                ],
                [
                    'type' => 'VISUAL_AB',
                    'isForcedVariationEnabled' => false,
                    'goals' => [
                        [
                            'type' => 'CUSTOM_GOAL',
                            'id' => 271,
                            'identifier' => 'CUSTOM'
                        ]
                    ],
                    'percentTraffic' => 100,
                    'id' => 32,
                    'name' => 'test3',
                    'key' => 'test3',
                    'status' => 'RUNNING',
                    'variations' => [
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 1,
                            'name' => 'Control'
                        ],
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 2,
                            'name' => 'Variation-1'
                        ]
                    ]
                ],
                [
                    'type' => 'VISUAL_AB',
                    'isForcedVariationEnabled' => false,
                    'goals' => [
                        [
                            'type' => 'CUSTOM_GOAL',
                            'id' => 271,
                            'identifier' => 'CUSTOM'
                        ]
                    ],
                    'percentTraffic' => 100,
                    'id' => 33,
                    'name' => 'test4',
                    'key' => 'test4',
                    'status' => 'RUNNING',
                    'variations' => [
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 1,
                            'name' => 'Control'
                        ],
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 2,
                            'name' => 'Variation-1'
                        ]
                    ]
                ],
                [
                    'type' => 'VISUAL_AB',
                    'isForcedVariationEnabled' => false,
                    'goals' => [
                        [
                            'type' => 'CUSTOM_GOAL',
                            'id' => 271,
                            'identifier' => 'CUSTOM'
                        ]
                    ],
                    'percentTraffic' => 100,
                    'id' => 34,
                    'name' => 'test5',
                    'key' => 'test5',
                    'status' => 'RUNNING',
                    'variations' => [
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 1,
                            'name' => 'Control'
                        ],
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 2,
                            'name' => 'Variation-1'
                        ]
                    ]
                ],
                [
                    'type' => 'VISUAL_AB',
                    'isForcedVariationEnabled' => false,
                    'goals' => [
                        [
                            'type' => 'CUSTOM_GOAL',
                            'id' => 271,
                            'identifier' => 'CUSTOM'
                        ]
                    ],
                    'percentTraffic' => 100,
                    'id' => 35,
                    'name' => 'test6',
                    'key' => 'test6',
                    'status' => 'RUNNING',
                    'variations' => [
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 1,
                            'name' => 'Control'
                        ],
                        [
                            'changes' => [],
                            'weight' => 50,
                            'id' => 2,
                            'name' => 'Variation-1'
                        ]
                    ]
                ],
            ],
            "accountId" => 1,
            "groups" => [
                3 => [
                    'et' => 2 ,
                    'campaigns' => [ 30, 31, 32, 33, 34 ],
                    'wt' => [ 31 => 80, 33 => 20 ],
                    'name' => 'Group3'
                ]
            ],
            'campaignGroups' => [
                30 => 3, 31 => 3, 32 => 3 , 33 => 3, 34 => 3
            ]
        ];
    }
}
