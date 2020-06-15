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

use PHPUnit\Framework\TestCase;
use Exception as Exception;
use vwo\Utils\SegmentEvaluator;

/***
 * Class VWOTest
 *
 * @package vwo
 */
class VWOTest extends TestCase
{

    private $vwoInstance;
    private $variationResults = '';
    private $segmentEvaluatorJson = '';

    protected function setUp()
    {
        $this->users = TestUtil::getUsers();

        $this->settings1 = Settings1::use();
        $this->settings2 = Settings2::use();
        $this->settings3 = Settings3::use();
        $this->settings4 = Settings4::use();
        $this->settings5 = Settings5::use();
        $this->settings6 = Settings6::use();
        $this->settings7 = Settings7::use();
        $this->settings8 = Settings8::use();
        $segmentEvaluatorJson = new SegmentEvaluatorJson();
        $results = new VariationResults();

        $this->variationResults = $results->results;
        $this->segmentEvaluatorJson = $segmentEvaluatorJson->setting;
    }

    public function testWithWrongConfig()
    {
        $obj = new VWO([]);
        $this->assertEquals(true, is_object($obj));
    }

    function testWithCorruptedSettingsFile()
    {
        $obj = TestUtil::instantiateSdk([
            'sdkKey' => 123,
            'accountId' => 1.9
        ]);

        $variation = $obj->activate('DEV_TEST_1', 'random-user');
        $isFeatureEnabled = $obj->isFeatureEnabled('DEV_TEST_8', 'random-user');

        $this->assertEquals(null, $variation);
        $this->assertEquals(null, $isFeatureEnabled);
    }


    public function testGetSettingsFile()
    {
        $response = VWO::getSettingsFile(1, 'randomKey');
        $this->assertEquals(false, $response);
    }

    public function testApiException()
    {
        $obj = TestUtil::instantiateSdk($this->settings8);
        $obj->variationDecider = TestUtil::mockMethodToThrowEception($this, 'VariationDecider', 'fetchVariationData');

        foreach ($this->users as $userId) {
            $value1 = $obj->isFeatureEnabled('FEATURE_ROLLOUT_ONLY', $userId);
            $value2 = $obj->getFeatureVariableValue('FEATURE_ROLLOUT_ONLY', 'V1', $userId);

            $this->assertEquals(true, is_null($value1) == false && $value2 == false);
            $this->assertEquals(true, is_null($value2));
        }

        $obj = TestUtil::instantiateSdk($this->settings1);
        $obj->variationDecider = TestUtil::mockMethodToThrowEception($this, 'VariationDecider', 'fetchVariationData');

        foreach ($this->users as $userId) {
            $value1 = $obj->activate('DEV_TEST_1', $userId);
            $value2 = $obj->getVariationName('DEV_TEST_1', $userId);
            $value3 = $obj->track('DEV_TEST_1', $userId, 'CUSTOM');

            $this->assertEquals(true, is_null($value1));
            $this->assertEquals(true, is_null($value2));
            $this->assertEquals(true, is_null($value3));
        }

        $obj = TestUtil::instantiateSdk($this->settings1);
        $obj->eventDispatcher = TestUtil::mockMethodToThrowEception($this, 'EventDispatcher', 'send');

        foreach ($this->users as $userId) {
            $value = $obj->push('tagKey', 'tagValue', $userId);
            $this->assertEquals(true, is_null($value) == false && $value == false);
        }
    }

    public function testDevModeEnabled()
    {

        $obj = TestUtil::instantiateSdk($this->settings8, ['isDevelopmentMode' => 1]);
        $obj->eventDispatcher = TestUtil::mockEventDispatcher($this);

        $userId = $this->users[rand(0, count($this->users) - 1)];

        $obj->activate('FEATURE_TEST', $userId);
        $obj->isFeatureEnabled('FEATURE_TEST', $userId);
        $obj->isFeatureEnabled(1234, 2342);
        $variationName = $obj->getVariationName('FEATURE_TEST', $userId);
    }

    public function testActivate()
    {
        for ($devtest = 1; $devtest < 7; $devtest++) {
            $this->vwoInstance = TestUtil::instantiateSdk($this->{'settings' . $devtest});
            $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
            $campaignKey = 'DEV_TEST_' . $devtest;
            foreach ($this->users as $userId) {
                $variationName = $this->vwoInstance->activate($campaignKey, $userId);
                $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
                $this->assertEquals($expected, $variationName);
            }
        }
    }

    public function testGetVariationName()
    {
        for ($devtest = 1; $devtest < 7; $devtest++) {
            $this->vwoInstance = TestUtil::instantiateSdk($this->{'settings' . $devtest});
            $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
            $campaignKey = 'DEV_TEST_' . $devtest;
            foreach ($this->users as $userId) {
                $variationName = $this->vwoInstance->getVariationName($campaignKey, $userId);
                $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
                $this->assertEquals($expected, $variationName);
            }
        }
    }

    public function testTrack()
    {
        for ($devtest = 1; $devtest < 7; $devtest++) {
            $settingsFile = $this->{'settings' . $devtest};

            $this->vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
            $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
            $campaignKey = 'DEV_TEST_' . $devtest;
            $options = [];
            for ($i = 0; $i < count($this->users); $i++) {
                $userId = $this->users[$i];
                foreach ($settingsFile['campaigns'] as $campaign) {
                    if ($campaign['key'] == $campaignKey) {
                        $goalname = $campaign['goals'][0]['identifier'];
                        if ($campaign['goals'][0]['type'] == 'REVENUE_TRACKING') {
                            $options['revenueValue'] = 10;
                        }
                        break;
                    }
                }
                $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
                $result = $this->vwoInstance->track($campaignKey, $userId, $goalname, $options);

                $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
                if ($expected == null) {
                    $expected = false;
                } else {
                    $expected = true;
                }
                $this->assertEquals($expected, $result);
            }
        }
    }

    public function testTrackForFeatureRollout()
    {
        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

        $campaignKey = 'FEATURE_ROLLOUT_ONLY';

        foreach ($this->users as $userId) {
            $response = $this->vwoInstance->track($campaignKey, $userId, 'CUSTOM');

            $this->assertEquals(true, is_null($response));
        }
    }

    public function testTrackForRevenueGoal()
    {
        $this->vwoInstance = TestUtil::instantiateSdk($this->settings2, ['isDevelopmentMode' => 1]);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

        $campaignKey = $this->settings2['campaigns'][0]['key'];

        foreach ($this->users as $userId) {
            $goalNotPresentResponse = $this->vwoInstance->track($campaignKey, $userId, 'UNDEFINED_GOAL_IDENTIFIER');
            $revenueGoalButNoValue = $this->vwoInstance->track($campaignKey, $userId, 'REVENUE');
            $revenueGoalWithValueResponse = $this->vwoInstance->track($campaignKey, $userId, 'REVENUE', [ 'revenueValue' => 10]);

            $this->assertEquals(true, is_null($goalNotPresentResponse));
            $this->assertEquals(false, $revenueGoalButNoValue);
            $this->assertEquals(true, is_null($revenueGoalWithValueResponse) == false && $revenueGoalWithValueResponse == true);
        }
    }

    public function testTrackForUser()
    {
        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8, ['isUserStorage' => 1]);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
        $campaignKey = $this->settings8['campaigns'][2]['key'];
        $userId = $this->users[0];
        $goalname = 'dsa';
        $result = $this->vwoInstance->track($campaignKey, $userId, $goalname);
        $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
        if ($expected == null) {
            $expected = false;
        } else {
            $expected = true;
        }
        $this->assertEquals($expected, $result);
    }

    public function testIsFeatureEnabled()
    {

        for ($devtest = 8; $devtest < 9; $devtest++) {
            $this->vwoInstance = TestUtil::instantiateSdk($this->{'settings' . $devtest});
            $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

            $featureTestKey = 'FEATURE_TEST';
            $featureTestVaritaionsDisabledKey = 'FEATURE_TEST_ALL_DISABLED';
            $featureTestZeroTrafficKey = 'FEATURE_TEST_ZERO_TRAFFIC';
            $featureRolloutZeroTrafficKey = 'FEATURE_ROLLOUT_ZERO_TRAFFIC';

            foreach ($this->users as $userId) {
                $isFeatureEnabled = $this->vwoInstance->isFeatureEnabled($featureTestKey, $userId);
                $isFeatureEnabledForZero = $this->vwoInstance->isFeatureEnabled($featureTestZeroTrafficKey, $userId);
                $isAllfeaturesDisbled = $this->vwoInstance->isFeatureEnabled($featureTestVaritaionsDisabledKey, $userId);
                $isFeatureRolloutForZeroTraffic = $this->vwoInstance->isFeatureEnabled($featureRolloutZeroTrafficKey, $userId);

                // false in case of feature rollout when traffic is zero
                // as in php false == null gives true then we have $isFeatureRolloutForZeroTraffic= false every time in this case . so handlng this here
                $this->assertEquals(true, is_null($isFeatureRolloutForZeroTraffic) == false && $isFeatureRolloutForZeroTraffic == false);

                // null everytime as traffic is zero
                $this->assertEquals(true, is_null($isFeatureEnabledForZero));

                // false when all isFeatureEnabled is false
                $this->assertEquals(true, is_null($isAllfeaturesDisbled) == false && $isAllfeaturesDisbled == false);
            }

            $isFeatureEnabledForWrongCampaign = $this->vwoInstance->isFeatureEnabled('campaign_not_running_key', $this->users[0]);
            $this->assertEquals(true, is_null($isFeatureEnabledForWrongCampaign));
        }

        $abCampaignKey = $this->settings1['campaigns'][0]['key'];

        $this->vwoInstance = TestUtil::instantiateSdk($this->settings1);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

        $isEnabled = $this->vwoInstance->isFeatureEnabled($abCampaignKey, $this->users[0]);
        $this->assertEquals(true, is_null($isEnabled));

        $variableValue = $this->vwoInstance->getFeatureVariableValue($abCampaignKey, 'variable-key', $this->users[0]);
        $this->assertEquals(true, is_null($isEnabled));
    }

    function testGetFeatureVariableValueForFeatureRollout()
    {
        $campaignKey = 'FEATURE_ROLLOUT_ONLY';

        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

        foreach ($this->users as $userId) {
            $variableV1 = $this->vwoInstance->getFeatureVariableValue($campaignKey, 'V1', $userId);

            $this->assertEquals(10, $variableV1);
        }
    }

    function testGetFeatureVariableValueForFeatureCampaign()
    {
        $featureTestKey = $this->settings8['campaigns'][1]['key'];

        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

        foreach ($this->users as $userId) {
            $variation = $this->vwoInstance->getVariationName($featureTestKey, $userId);

            switch ($variation) {
                case 'Control':
                    $expectedIsFeatureEnabled = true;
                    $expectedFeatureVariableValue = 10;
                    break;
                case 'Variation-1':
                    $expectedIsFeatureEnabled = false;
                    $expectedFeatureVariableValue = 10;
                    break;
                case 'Variation-2':
                    $expectedIsFeatureEnabled = true;
                    $expectedFeatureVariableValue = 20;
                    break;
            }

            $campaignKeyWrong = $this->vwoInstance->getFeatureVariableValue(123, 'V!', $userId);
            $userIdWrong = $this->vwoInstance->getFeatureVariableValue($featureTestKey, 'V!', 123);

            $featureVariableValue = $this->vwoInstance->getFeatureVariableValue($featureTestKey, 'V1', $userId);
            $featureVariableNotFoundValue = $this->vwoInstance->getFeatureVariableValue($featureTestKey, 'no-such-variable', $userId);

            $this->assertEquals(true, is_null($campaignKeyWrong));
            $this->assertEquals(true, is_null($userIdWrong));
            $this->assertEquals($expectedFeatureVariableValue, $featureVariableValue);
            $this->assertEquals(true, is_null($featureVariableNotFoundValue));
        }
    }

    public function testPushApi()
    {
        $userId = $this->users[rand(0, count($this->users) - 1)];

        $cases = [
            //empty case
            ['tagKey' => '', 'tagValue' => '', 'expected' => false],
            //length check
            ['tagKey' => 'qwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuiopptyeytry',
                'tagValue' => 'qwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuiopptyeytry',
                'expected' => false],
            //datatype case
            ['tagKey' => 1, 'tagValue' => 2, 'expected' => false],
            //happy case
            ['tagKey' => 'foo', 'tagValue' => 'bar', 'expected' => true],
        ];

        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8, ['isUserStorage' => 1]);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);

        foreach ($cases as $case) {
            $response = $this->vwoInstance->push($case['tagKey'], $case['tagValue'], $userId);
            $this->assertEquals($case['expected'], $response);
        }

        $this->vwoInstance = TestUtil::instantiateSdk($this->settings8, ['isUserStorage' => 1, 'isDevelopmentMode' => 1]);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this, 400);

        foreach ($cases as $case) {
            $response = $this->vwoInstance->push($case['tagKey'], $case['tagValue'], $userId);
            $this->assertEquals($case['expected'], $response);
        }
    }

    public function testSegmentEvaluator()
    {
        $whitlistingEvaluatorJson = new SegmentEvaluatorJson();
        $segmentData = json_decode(str_replace('\\', '\\\\', $whitlistingEvaluatorJson->setting), 1);

        foreach ($segmentData as $key => $segments) {
            foreach ($segments as $segment) {
                $segmentObj = new SegmentEvaluator();
                $res = $segmentObj->evaluate($segment['dsl'], $segment['customVariables']);
                $this->assertEquals($segment['expectation'], $res);
            }
        }
        $segmentObj = new SegmentEvaluator();
        $resForEmptyDSL = $segmentObj->evaluate([], []);
        $this->assertEquals(true, $resForEmptyDSL);
    }

    public function testWhitelisting()
    {
        $data = ['camapaignKey' => 'DEV_TEST_6', 'userId' => 'user_1'];
        $whitelistingTags = [
            'chrome' => false,
            'safari' => true,
            'browser' => 'chrome 107.107'
        ];
        $falseWhiteListingTags = [
            'chrome' => true,
            'safari' => false,
            'browser' => 'firefox 106.69'
        ];

        $whitelistingObj = new WhitelistingJson();

        $whitelistingSetting = $whitelistingObj->setting;
        $customVariables = ['contains_vwo' => 'qqvwoqq', 'regex_for_all_letters' => 'abc', 'regex_for_small_letters' => 'www', 'regex_for_zeros' => 0, 'regex_for_capital_letters' => 'ABC', 'regex_for_no_zeros' => 123, 'regex_real_number' => 123, 'starts_with' => 'vwo'];

        $this->vwoInstance = TestUtil::instantiateSdk($whitelistingSetting);
        $this->vwoInstance->eventDispatcher = TestUtil::mockEventDispatcher($this);
        $variationName = $this->vwoInstance->getVariationName($data['camapaignKey'], $data['userId'], ['variationTargetingVariables' => $whitelistingTags, 'customVariables' => $customVariables]);
        $variationNameForFalse = $this->vwoInstance->getVariationName($data['camapaignKey'], $data['userId'], ['variationTargetingVariables' => $falseWhiteListingTags, 'customVariables' => $customVariables]);

        $expected1 = 'Variation-2';
        $expected2 = 'Control';
        $this->assertEquals($expected1, $variationName);
        $this->assertEquals($expected2, $variationNameForFalse);
    }
}
