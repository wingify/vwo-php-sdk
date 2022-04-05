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

class FRWhitelistingSettings
{
    public static function setup()
    {
        return array(
            'sdkKey' => 'loremipsum123456',
            'campaigns' =>
                array(
                    0 =>
                        [
                            'id' => 93,
                            "name" => "Campaign-93",
                            'status' => 'RUNNING',
                            'percentTraffic' => 100,
                            'type' => 'FEATURE_ROLLOUT',
                            'variables' => [
                                [
                                    'value' => 10,
                                    'type' => 'integer',
                                    'key' => 'V1',
                                    'id' => 1,
                                ]
                            ],
                            'key' => 'FEATURE_ROLLOUT_KEY',
                            'variations' => [
                                [
                                    'id' => 1,
                                    'name' => 'Feature-Rollout',
                                    'changes' => [],
                                    'weight' => 100,
                                    'segments' =>
                                        [
                                            'or' =>
                                                [
                                                    [
                                                        "custom_variable" =>
                                                            [
                                                                "chrome" => "false"
                                                            ]
                                                    ]
                                                ]
                                        ]
                                ],
                            ],
                            'goals' => [
                                [
                                    'type' => 'ON_PAGE',
                                    'id' => 1,
                                    'identifier' => 'http://vwo_d_feature-rollout',
                                ]
                            ],
                            'isBucketingSeedEnabled' => true,
                            'isForcedVariationEnabled' => true,
                        ],
                ),
            'accountId' => 0,
            'version' => 1,
        );
    }
}
