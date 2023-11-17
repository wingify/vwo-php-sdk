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
use vwo\Utils\Validations as ValidationsUtil;

class ValidationsTest extends TestCase
{
    public function setUp(): void
    {
        $this->users = TestUtil::getUsers();
    }
    public function testTrackValidation()
    {
        $this->settings8 = Settings8::setup();

        $userId = $this->users[rand(0, count($this->users) - 1)];
        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8, ['isUserStorage' => 1]);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
        $resultForInvalidParams = $this->vwoInstance->track();
        $resultForRollout = $this->vwoInstance->track('FEATURE_ROLLOUT', $userId, 'CUSTOM_GOAL');
        $this->assertEquals(false, $resultForInvalidParams);
        $this->assertEquals(false, $resultForRollout);
    }

    public function testPushApiParams()
    {
        $userId = $this->users[rand(0, count($this->users) - 1)];

        $tagValue = 'qwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTest';
        $tagValue .= 'qwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTest';
        $tagValue .= 'qwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTest';
        list($invalidPushApiParams, $invalidDimensionsMap, $validCustomDimensionMap) = ValidationsUtil::pushApiParams('', ['abc' => '']);
        $this->assertEquals(false, $invalidPushApiParams);
        $this->assertEquals([], $invalidDimensionsMap);
        $this->assertEquals([], $validCustomDimensionMap);

        list($invalidPushApiParams, $invalidDimensionsMap, $validCustomDimensionMap) = ValidationsUtil::pushApiParams($userId, ['abc' => '']);
        $this->assertEquals(false, $invalidPushApiParams);
        $this->assertEquals(false, $invalidDimensionsMap['abc']);
        $this->assertEquals([], $validCustomDimensionMap);

        list($invalidPushApiParams, $invalidDimensionsMap, $validCustomDimensionMap) = ValidationsUtil::pushApiParams($userId, ['abc' => $tagValue]);
        $this->assertEquals(false, $invalidPushApiParams);
        $this->assertEquals(false, $invalidDimensionsMap['abc']);
        $this->assertEquals([], $validCustomDimensionMap);

        list($invalidPushApiParams, $invalidDimensionsMap, $validCustomDimensionMap) = ValidationsUtil::pushApiParams($userId, ['abc' => $tagValue, "xyz" => "demo"]);
        $this->assertEquals(true, $invalidPushApiParams);
        $this->assertEquals(false, $invalidDimensionsMap['abc']);
        $this->assertEquals(false, isset($invalidDimensionsMap['xyz']));
        $this->assertEquals(false, isset($validCustomDimensionMap['abc']));
        $this->assertEquals("demo", $validCustomDimensionMap['xyz']);
    }

    public function testValidateCampaignKey()
    {
        $campaignKeyError = ValidationsUtil::validateIsFeatureEnabledParams(1, 1);
        $checkSettingSchema = ValidationsUtil::checkSettingSchema('');
        $invalidUserId = ValidationsUtil::validateUserId(0);
        $this->assertEquals(false, $invalidUserId);
        $this->assertEquals(false, $campaignKeyError);
        $this->assertEquals(false, $checkSettingSchema);
    }

    public function testCheckPreSegmentation()
    {
        $userId = $this->users[rand(0, count($this->users) - 1)];
        $segment = json_decode('{"dsl":{"and":[{"custom_variable":{"eq":"eq_value"}}]},"expectation":true,"customVariables":{"eq":"eq_value"}}', 1);
        $camapign = [
            'key' => 'test',
            'segments' => $segment['dsl']
        ];
        $options = ['customVariables' => $segment['customVariables']];
        $res = ValidationsUtil::checkPreSegmentation($camapign, $userId, $options);
        $this->assertEquals(true, $res);
    }

    public function testValidateVariablesWithIncompleteData()
    {
        $checkSettingSchema = ValidationsUtil::checkSettingSchema(Settings7::setup());
        $this->assertEquals(false, $checkSettingSchema);
    }

    public function testValidateVariablesWithCompleteData()
    {
        $checkSettingSchema = ValidationsUtil::checkSettingSchema(Settings8::setup());
        $this->assertEquals(true, $checkSettingSchema);
    }

    public function testValidateVariablesForWrongJsonVariable()
    {
        $checkSettingSchema = ValidationsUtil::checkSettingSchema(Settings10::setup());
        $this->assertEquals(false, $checkSettingSchema);
    }
}
