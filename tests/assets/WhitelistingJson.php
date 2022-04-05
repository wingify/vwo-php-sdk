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

class WhitelistingJson
{
    var $setting = array (
        'sdkKey' => 'someuniquestuff1234567',
        'campaigns' =>
            array (
                0 =>
                    array (
                        'goals' =>
                            array (
                                0 =>
                                    array (
                                        'identifier' => 'CUSTOM',
                                        'id' => 218,
                                        'type' => 'CUSTOM_GOAL',
                                    ),
                            ),
                        'variations' =>
                            array (
                                0 =>
                                    array (
                                        'id' => 1,
                                        'name' => 'Control',
                                        'changes' =>
                                            array (
                                            ),
                                        'weight' => 33.3333,
                                        'segments' =>
                                            array (
                                                'or' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'user' => 'user_1'
                                                            ),
                                                    ),
                                            ),
                                    ),
                                1 =>
                                    array (
                                        'id' => 2,
                                        'name' => 'Variation-1',
                                        'changes' =>
                                            array (
                                            ),
                                        'weight' => 33.3333,
                                        'segments' =>
                                            array (
                                                'or' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'custom_variable' =>
                                                                    array (
                                                                        'browser' => 'wildcard(chrome*)',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                                2 =>
                                    array (
                                        'id' => 3,
                                        'name' => 'Variation-2',
                                        'changes' =>
                                            array (
                                            ),
                                        'weight' => 33.3333,
                                        'segments' =>
                                            array (
                                                'or' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'custom_variable' =>
                                                                    array (
                                                                        'chrome' => 'false',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                            ),
                        'id' => 235,
                        'percentTraffic' => 100,
                        'key' => 'DEV_TEST_6',
                        'status' => 'RUNNING',
                        'type' => 'VISUAL_AB',
                        'isForcedVariationEnabled' => true,
                        'segments' => [],
                        'segments1' =>
                            array (
                                'and' =>
                                    array (
                                        0 =>
                                            array (
                                                'or' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'custom_variable' =>
                                                                    array (
                                                                        'contains_vwo' => 'wildcard(*vwo*)',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                        1 =>
                                            array (
                                                'and' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'and' =>
                                                                    array (
                                                                        0 =>
                                                                            array (
                                                                                'or' =>
                                                                                    array (
                                                                                        0 =>
                                                                                            array (
                                                                                                'and' =>
                                                                                                    array (
                                                                                                        0 =>
                                                                                                            array (
                                                                                                                'or' =>
                                                                                                                    array (
                                                                                                                        0 =>
                                                                                                                            array (
                                                                                                                                'and' =>
                                                                                                                                    array (
                                                                                                                                        0 =>
                                                                                                                                            array (
                                                                                                                                                'or' =>
                                                                                                                                                    array (
                                                                                                                                                        0 =>
                                                                                                                                                            array (
                                                                                                                                                                'custom_variable' =>
                                                                                                                                                                    array (
                                                                                                                                                                        'regex_for_all_letters' => 'regex(^[A-z]+$)',
                                                                                                                                                                    ),
                                                                                                                                                            ),
                                                                                                                                                    ),
                                                                                                                                            ),
                                                                                                                                        1 =>
                                                                                                                                            array (
                                                                                                                                                'or' =>
                                                                                                                                                    array (
                                                                                                                                                        0 =>
                                                                                                                                                            array (
                                                                                                                                                                'custom_variable' =>
                                                                                                                                                                    array (
                                                                                                                                                                        'regex_for_capital_letters' => 'regex(^[A-Z]+$)',
                                                                                                                                                                    ),
                                                                                                                                                            ),
                                                                                                                                                    ),
                                                                                                                                            ),
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                        1 =>
                                                                                                                            array (
                                                                                                                                'or' =>
                                                                                                                                    array (
                                                                                                                                        0 =>
                                                                                                                                            array (
                                                                                                                                                'custom_variable' =>
                                                                                                                                                    array (
                                                                                                                                                        'regex_for_small_letters' => 'regex(^[a-z]+$)',
                                                                                                                                                    ),
                                                                                                                                            ),
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                    ),
                                                                                                            ),
                                                                                                        1 =>
                                                                                                            array (
                                                                                                                'or' =>
                                                                                                                    array (
                                                                                                                        0 =>
                                                                                                                            array (
                                                                                                                                'custom_variable' =>
                                                                                                                                    array (
                                                                                                                                        'regex_for_no_zeros' => 'regex(^[1-9]+$)',
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                    ),
                                                                                                            ),
                                                                                                    ),
                                                                                            ),
                                                                                        1 =>
                                                                                            array (
                                                                                                'or' =>
                                                                                                    array (
                                                                                                        0 =>
                                                                                                            array (
                                                                                                                'custom_variable' =>
                                                                                                                    array (
                                                                                                                        'regex_for_zeros' => 'regex(^[0]+$)',
                                                                                                                    ),
                                                                                                            ),
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                        1 =>
                                                                            array (
                                                                                'or' =>
                                                                                    array (
                                                                                        0 =>
                                                                                            array (
                                                                                                'custom_variable' =>
                                                                                                    array (
                                                                                                        'regex_real_number' => 'regex(^\\d+(\\.\\d+)?)',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                        1 =>
                                                            array (
                                                                'or' =>
                                                                    array (
                                                                        0 =>
                                                                            array (
                                                                                'or' =>
                                                                                    array (
                                                                                        0 =>
                                                                                            array (
                                                                                                'custom_variable' =>
                                                                                                    array (
                                                                                                        'this_is_regex' => 'regex(this\\s+is\\s+text)',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                        1 =>
                                                                            array (
                                                                                'and' =>
                                                                                    array (
                                                                                        0 =>
                                                                                            array (
                                                                                                'and' =>
                                                                                                    array (
                                                                                                        0 =>
                                                                                                            array (
                                                                                                                'or' =>
                                                                                                                    array (
                                                                                                                        0 =>
                                                                                                                            array (
                                                                                                                                'custom_variable' =>
                                                                                                                                    array (
                                                                                                                                        'starts_with' => 'wildcard(starts_with_variable*)',
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                    ),
                                                                                                            ),
                                                                                                        1 =>
                                                                                                            array (
                                                                                                                'or' =>
                                                                                                                    array (
                                                                                                                        0 =>
                                                                                                                            array (
                                                                                                                                'custom_variable' =>
                                                                                                                                    array (
                                                                                                                                        'contains' => 'wildcard(*contains_variable*)',
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                    ),
                                                                                                            ),
                                                                                                    ),
                                                                                            ),
                                                                                        1 =>
                                                                                            array (
                                                                                                'or' =>
                                                                                                    array (
                                                                                                        0 =>
                                                                                                            array (
                                                                                                                'not' =>
                                                                                                                    array (
                                                                                                                        'or' =>
                                                                                                                            array (
                                                                                                                                0 =>
                                                                                                                                    array (
                                                                                                                                        'custom_variable' =>
                                                                                                                                            array (
                                                                                                                                                'is_not_equal_to' => 'is_not_equal_to_variable',
                                                                                                                                            ),
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                    ),
                                                                                                            ),
                                                                                                        1 =>
                                                                                                            array (
                                                                                                                'or' =>
                                                                                                                    array (
                                                                                                                        0 =>
                                                                                                                            array (
                                                                                                                                'custom_variable' =>
                                                                                                                                    array (
                                                                                                                                        'is_equal_to' => 'equal_to_variable',
                                                                                                                                    ),
                                                                                                                            ),
                                                                                                                    ),
                                                                                                            ),
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                            ),
                    ),
            ),
        'accountId' => 888888,
        'version' => 1,
    );
}
