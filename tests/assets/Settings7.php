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

class Settings7
{
    public static function use()
    {
        return [
            "featureFlags" => [[
                "id" => 1,
                "key" => "FEATURE_TEST",
                "status" => "RUNNING",
                "percentTraffic" => 50,
                "campaignIds" => [22],
                "variables" => [[
                    "id" => 1,
                    "key" => "STRING_VARIABLE",
                    "type" => "string",
                    "defaultValue" => "d1"
                    ], [
                        "id" => 2,
                        "key" => "INTEGER_VARIABLE",
                        "type" => "integer",
                        "defaultValue" => 123
                    ], [
                        "id" => 3,
                        "key" => "BOOLEAN_VARIABLE",
                        "type" => "boolean",
                        "defaultValue" => true
                    ], [
                        "id" => 4,
                        "key" => "DOUBLE_INTEGER",
                        "type" => "double",
                        "defaultValue" => 123.45
                ]],
                "variations" => [[
                    "id" => "1",
                    "name" => "Control",
                    "weight" => 100
                ]]
            ], [
                "id" => 2,
                "key" => "FEATURE_ROLLOUT_ONLY",
                "status" => "RUNNING",
                "percentTraffic" => 50,
                "campaignIds" => [],
                "variables" => [[
                    "id" => 1,
                    "key" => "STRING_VARIABLE",
                    "type" => "string",
                    "defaultValue" => "d1"
                    ], [
                    "id" => 2,
                    "key" => "INTEGER_VARIABLE",
                    "type" => "integer",
                    "defaultValue" => 123
                    ], [
                        "id" => 3,
                        "key" => "BOOLEAN_VARIABLE",
                        "type" => "boolean",
                        "defaultValue" => true
                    ], [
                        "id" => 4,
                        "key" => "DOUBLE_INTEGER",
                        "type" => "double",
                        "defaultValue" => 123.45
                    ]
                ],
                "variations" => [[
                    "id" => "1",
                    "name" => "Control",
                    "weight" => 100
                ]]
                ]],
                "campaigns" => [[
                    "goals" => [[
                        "identifier" => "FEATURE_TEST_GOAL",
                        "id" => 203,
                        "type" => "CUSTOM_GOAL"
                    ]],
                    "variations" => [[
                        "id" => "1",
                        "name" => "Control",
                        "weight" => 50,
                        "variables" => [[
                            "id" => 1,
                            "key" => "",
                            "value" => "c1"
                        ], [
                            "id" => 2,
                            "value" => 10
                        ], [
                            "id" => 3,
                            "value" => true
                        ], [
                            "id" => 4,
                            "value" => 10.65
                        ]],
                        "isFeatureEnabled" => false
                    ], [
                    "id" => "2",
                    "name" => "Variation-1",
                    "weight" => 50,
                    "variables" => [[
                        "id" => 1,
                        "value" => "v1"
                    ], [
                        "id" => 2,
                        "value" => 20
                    ]],
                    "isFeatureEnabled" => true
                    ]
                    ],
                    "id" => 22,
                    "percentTraffic" => 50,
                    "key" => "FEATURE_TEST",
                    "status" => "RUNNING",
                    "type" => "FEATURE_TEST"
                ], [
                    "goals" => [[
                        "identifier" => "CUSTOM_RECOMMENDATION_AB_GOAL",
                        "id" => 203,
                        "type" => "CUSTOM_GOAL"
                    ]],
                    "variations" => [[
                        "id" => "1",
                        "name" => "Control",
                        "weight" => 40
                    ], [
                        "id" => "2",
                        "name" => "Variation-1",
                        "weight" => 60
                    ]],
                    "id" => 22,
                    "percentTraffic" => 90,
                    "key" => "NEW_RECOMMENDATION_AB_CAMPAIGN",
                    "status" => "RUNNING",
                    "type" => "VISUAL_AB"
                ]
            ],
            "accountId" => 123456,
            "version" => 2
        ];
    }
}
