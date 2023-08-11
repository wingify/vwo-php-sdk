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

use PHPUnit\Framework\TestCase;
use vwo\Constants\EventEnum;
use vwo\Utils\Common as CommonUtil;
use vwo\Utils\ImpressionBuilder;

class ImpressionBuilderTest extends TestCase
{
    const EVENT_ARCH_QUERY_PARAMS = ['a', 'en', 'eTime', 'random', 'env', 'p'];
    public function testBuildEventArchPayloadForVisitor()
    {
        $accountId = 1;
        $sdkKey = '12345';
        $queryParams = ImpressionBuilder::getEventsBaseProperties($accountId, $sdkKey, EventEnum::VWO_VARIATION_SHOWN);
        $properties = ImpressionBuilder::getTrackUserPayloadData(["accountId" => $accountId, "sdkKey" => $sdkKey], 'Ashley', EventEnum::VWO_VARIATION_SHOWN, 20, 3, ['_l' => 1, 'cl' => 1, 'll' => 1]);
        $expectedProperties = [
            "d" => [
                "msgId" => "string",
                "visId" => "string",
                "sessionId" => 123,
                "event" => [
                    "props" => [
                        'vwo_sdkName' => "string",
                        'vwo_sdkVersion' => "string",
                        'id' => 12,
                        'isFirst' => 1233,
                        'variation' => 2,
                    ],
                    'name' => "string",
                    'time' => 123.45
                ],
                "visitor" => [
                    "props" => [
                        "vwo_fs_environment" => "string"
                    ]
                ]
            ]
        ];

        $allPresent = $this->checkAllPropertiesPresent($queryParams, self::EVENT_ARCH_QUERY_PARAMS);
        $this->assertEquals(true, $allPresent);
        $allPresentAndValid = $this->checkAllPropertiesPresentAndTheirTypes($properties, $expectedProperties);
        $this->assertEquals(true, $allPresentAndValid);
    }

    public function testBuildEventArchPayloadForGoal()
    {
        $accountId = 1;
        $sdkKey = '12345';
        $config = ["accountId" => 1, "sdkKey" => '12345'];
        $goalIdentifier = 'goalIdentifier';
        $metricMap = [
            "1" => 10,
            "2" => 20,
            "5" => 30
        ];
        $dummyRevenueProperty = ['dummyRevenueProperty'];
        $queryParams = ImpressionBuilder::getEventsBaseProperties($accountId, $sdkKey, $goalIdentifier);
        $properties = ImpressionBuilder::getTrackGoalPayloadData($config, 'Ashley', $goalIdentifier, $metricMap, [ "dummyRevenueProperty" => 12 ]);
        $expectedProperties = [
            "d" => [
                "msgId" => "string",
                "visId" => "string",
                "sessionId" => 123,
                "event" => [
                    "props" => [
                        'vwo_sdkName' => "string",
                        'vwo_sdkVersion' => "string",
                        "vwo_envKey" => "string",
                        'vwoMeta' => [
                            'metric' => [
                                "id_1" => ["g_10"],
                                "id_2" => ["g_20"],
                                "id_5" => ["g_30"]
                            ]
                        ],
                        $dummyRevenueProperty[0] => 12,
                        'isCustomEvent' => true,
                    ],
                    'name' => "string",
                    'time' => 123.45
                ],
                "visitor" => [
                    "props" => [
                        "vwo_fs_environment" => "string"
                    ]
                ]
            ]
        ];
        $allPresent = $this->checkAllPropertiesPresent($queryParams, self::EVENT_ARCH_QUERY_PARAMS);
        $this->assertEquals(true, $allPresent);
        $allPresentAndValid = $this->checkAllPropertiesPresentAndTheirTypes($properties, $expectedProperties);
        $this->assertEquals(true, $allPresentAndValid);
    }

    public function testBuildEventArchPayloadForPush()
    {
        $accountId = 1;
        $sdkKey = '12345';
        $config = ["accountId" => 1, "sdkKey" => '12345'];
        $queryParams = ImpressionBuilder::getEventsBaseProperties($accountId, $sdkKey, EventEnum::VWO_SYNC_VISITOR_PROP);
        $properties = ImpressionBuilder::getPushPayloadData($config, 'Ashley', EventEnum::VWO_SYNC_VISITOR_PROP, ['tagKey' => 'tagValue']);
        $expectedProperties = [
            "d" => [
                "msgId" => "string",
                "visId" => "string",
                "sessionId" => 123,
                "event" => [
                    "props" => [
                        'vwo_sdkName' => "string",
                        'vwo_sdkVersion' => "string",
                        "vwo_envKey" => "string",
                        'isCustomEvent' => true,
                    ],
                    'name' => "string",
                    'time' => 123.45
                ],
                "visitor" => [
                    "props" => [
                        "vwo_fs_environment" => "string",
                        'tagKey' => 'tagValue'
                    ]
                ]
            ]
        ];

        $allPresent = $this->checkAllPropertiesPresent($queryParams, self::EVENT_ARCH_QUERY_PARAMS);
        $this->assertEquals(true, $allPresent);

        $allPresentAndValid = $this->checkAllPropertiesPresentAndTheirTypes($properties, $expectedProperties);
        $this->assertEquals(true, $allPresentAndValid);
    }

    public function checkAllPropertiesPresent($properties, $expectedProperties)
    {
        foreach ($expectedProperties as $field) {
            if (!isset($properties[$field])) {
                return false;
            }
        }
        return true;
    }

    public function checkAllPropertiesPresentAndTheirTypes($properties, $expectedProperties)
    {
        foreach ($expectedProperties as $key => $value) {
            if (!isset($properties[$key]) || (gettype($properties[$key]) != gettype($expectedProperties[$key]))) {
                return false;
            } elseif (is_array($properties[$key])) {
                $allPresent = $this->checkAllPropertiesPresentAndTheirTypes($properties[$key], $expectedProperties[$key]);
                if (!$allPresent) {
                    return false;
                }
            }
        }
        return true;
    }

    public function testgetEventsBasePropertiesArchEnabledWithUAAndIPTest(){
        $accountId = 1;
        $sdkKey = '12345';
        $queryParams = ImpressionBuilder::getEventsBaseProperties($accountId, $sdkKey, EventEnum::VWO_SYNC_VISITOR_PROP,"abcBot","123.10.234");
        
        $containsVisitorUa = array_key_exists("visitor_ua", $queryParams);
        $containsVisitorIp = array_key_exists("visitor_ip", $queryParams);
        $this->assertEquals(true, $containsVisitorUa);
        $this->assertEquals(true, $containsVisitorIp);
    }

    public function testgetVisitorQueryParamsWithUAAndIPTest(){
        $settingsFileEventProperties = SettingsFileEventProperties::setUp();
        $campaign = $settingsFileEventProperties['campaigns'][0];
        $visitorUserAgent = "abcBot";
        $userIpAddress = "123.10.123";
        
        $parameters = ImpressionBuilder::getVisitorQueryParams(
            $settingsFileEventProperties['accountId'],
            $campaign,
            '123',
            '2',
            $settingsFileEventProperties['sdkKey'],
            $visitorUserAgent,
            $userIpAddress
        );

        $containsVisitorUa = array_key_exists("visitor_ua", $parameters);
        $containsVisitorIp = array_key_exists("visitor_ip", $parameters);
        $this->assertEquals(true, $containsVisitorUa);
        $this->assertEquals(true, $containsVisitorIp);
    }

    public function testgetConversionQueryParamsArchEnabledWithUAAndIPTest(){
        $settingsFileEventProperties = SettingsFileEventProperties::setUp();
        $campaign = $settingsFileEventProperties['campaigns'][0];
        $visitorUserAgent = "abcBot";
        $userIpAddress = "123.10.123";
        
        $parameters = ImpressionBuilder::getConversionQueryParams(
            $settingsFileEventProperties['accountId'],
            $campaign,
            '123',
            '2',
            $settingsFileEventProperties['campaigns'][0]['goals'][0],
            '100',
            $settingsFileEventProperties['sdkKey'],
            $visitorUserAgent,
            $userIpAddress
        );

        $containsVisitorUa = array_key_exists("visitor_ua", $parameters);
        $containsVisitorIp = array_key_exists("visitor_ip", $parameters);
        $this->assertEquals(true, $containsVisitorUa);
        $this->assertEquals(true, $containsVisitorIp);
    }
    


}
