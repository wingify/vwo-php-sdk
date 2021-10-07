<?php

/**
 * Copyright 2019-2021 Wingify Software Pvt. Ltd.
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

class Settings8
{
    public static function setup()
    {
        return array(
            'sdkKey' => 'loremipsum123456',
            'campaigns' =>
                array(
                    0 =>
                        array(
                            'id' => 91,
                            "name" => "Campaign-91",
                            'segments' =>
                                array(),
                            'status' => 'RUNNING',
                            'percentTraffic' => 100,
                            'type' => 'FEATURE_ROLLOUT',
                            'segments' => [],
                            'variables' =>
                                array(
                                    0 =>
                                        array(
                                            'value' => 10,
                                            'type' => 'integer',
                                            'key' => 'V1',
                                            'id' => 1,
                                        ),
                                ),
                            'key' => 'FEATURE_ROLLOUT_ONLY',
                            'variations' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 1,
                                            'name' => 'website',
                                            'changes' =>
                                                array(),
                                            'weight' => 100,
                                        ),
                                ),
                            'goals' =>
                                array(
                                    0 =>
                                        array(
                                            'identifier' => 'h',
                                            'id' => 209,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                    1 =>
                                        array(
                                            'identifier' => 'CUSTOM',
                                            'id' => 210,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                ),
                        ),
                    1 =>
                        array(
                            'percentTraffic' => 100,
                            'segments' => [],
                            'goals' =>
                                array(
                                    0 =>
                                        array(
                                            'identifier' => 'CUSTOME',
                                            'id' => 409,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                ),
                            'variations' =>
                                array(
                                    0 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 1,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 10,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => true,
                                            'weight' => 50,
                                            'name' => 'Control',
                                        ),
                                    1 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 2,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 20,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 25,
                                            'name' => 'Variation-1',
                                        ),
                                    2 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 2,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 20,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 25,
                                            'name' => 'Variation-1',
                                        ),
                                ),
                            'id' => 92,
                            "name" => "Campaign-92",
                            'segments' =>
                                array(),
                            'key' => 'FEATURE_TEST',
                            'status' => 'RUNNING',
                            'type' => 'FEATURE_TEST',
                        ),
                    2 =>
                        array(
                            'percentTraffic' => 67,
                            'segments' => [],
                            'goals' =>
                                array(
                                    0 =>
                                        array(
                                            'identifier' => 'dsa',
                                            'id' => 411,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                ),
                            'variations' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 1,
                                            'name' => 'Control',
                                            'changes' =>
                                                array(),
                                            'weight' => 50,
                                        ),
                                    1 =>
                                        array(
                                            'id' => 2,
                                            'name' => 'Variation-1',
                                            'changes' =>
                                                array(),
                                            'weight' => 50,
                                        )
                                ),
                            array(
                                'goals' => array(
                                    array(
                                        'identifier' => 'CUSTOM',
                                        'id' => 213,
                                        'type' => 'CUSTOM_GOAL'
                                    )
                                )
                            ),
                            'id' => 94,
                            "name" => "Campaign-94",
                            'segments' =>
                                array(),
                            'key' => 'DEV_TEST_8',
                            'status' => 'RUNNING',
                            'type' => 'VISUAL_AB',
                        ),
                    3 =>
                        array(
                            'percentTraffic' => 100,
                            'segments' => [],
                            'goals' =>
                                array(
                                    0 =>
                                        array(
                                            'identifier' => 'CUSTOME',
                                            'id' => 409,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                ),
                            'variations' =>
                                array(
                                    0 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 1,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 10,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 50,
                                            'name' => 'Control',
                                        ),
                                    1 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 2,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 20,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 25,
                                            'name' => 'Variation-1',
                                        ),
                                    2 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 2,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 20,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 25,
                                            'name' => 'Variation-1',
                                        ),
                                ),
                            'id' => 92,
                            "name" => "Campaign-92",
                            'segments' =>
                                array(),
                            'key' => 'FEATURE_TEST_ALL_DISABLED',
                            'status' => 'RUNNING',
                            'type' => 'FEATURE_TEST',
                        ),
                    4 =>
                        array(
                            'percentTraffic' => 0,
                            'segments' => [],
                            'goals' =>
                                array(
                                    0 =>
                                        array(
                                            'identifier' => 'CUSTOME',
                                            'id' => 409,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                ),
                            'variations' =>
                                array(
                                    0 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 1,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 10,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 50,
                                            'name' => 'Control',
                                        ),
                                    1 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 2,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 20,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 25,
                                            'name' => 'Variation-1',
                                        ),
                                    2 =>
                                        array(
                                            'changes' =>
                                                array(),
                                            'id' => 2,
                                            'variables' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'value' => 20,
                                                            'type' => 'integer',
                                                            'key' => 'V1',
                                                            'id' => 1,
                                                        ),
                                                ),
                                            'isFeatureEnabled' => false,
                                            'weight' => 25,
                                            'name' => 'Variation-1',
                                        ),
                                ),
                            'id' => 92,
                            "name" => "Campaign-92",
                            'segments' =>
                                array(),
                            'key' => 'FEATURE_TEST_ZERO_TRAFFIC',
                            'status' => 'RUNNING',
                            'type' => 'FEATURE_TEST',
                        ),
                    5 =>
                        array(
                            'id' => 91,
                            "name" => "Campaign-91",
                            'segments' =>
                                array(),
                            'status' => 'RUNNING',
                            'percentTraffic' => 0,
                            'type' => 'FEATURE_ROLLOUT',
                            'segments' => [],
                            'variables' =>
                                array(
                                    0 =>
                                        array(
                                            'value' => 10,
                                            'type' => 'integer',
                                            'key' => 'V1',
                                            'id' => 1,
                                        ),
                                ),
                            'key' => 'FEATURE_ROLLOUT_ZERO_TRAFFIC',
                            'variations' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 1,
                                            'name' => 'website',
                                            'changes' =>
                                                array(),
                                            'weight' => 100,
                                        ),
                                ),
                            'goals' =>
                                array(
                                    0 =>
                                        array(
                                            'identifier' => 'h',
                                            'id' => 209,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                    1 =>
                                        array(
                                            'identifier' => 'jk',
                                            'id' => 210,
                                            'type' => 'CUSTOM_GOAL',
                                        ),
                                ),
                        ),
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
                                            'value' => ["a"=>3],
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
