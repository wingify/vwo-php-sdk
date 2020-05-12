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
use \Exception as Exception;
use vwo\Storage\UserStorageInterface;
use vwo\Logger\LoggerInterface;
use vwo\Utils\SegmentEvaluator;
use vwo\Utils\Validations;
use vwo\Utils\Campaign;

/**
 * Class CustomLogger
 */
class CustomLogger implements LoggerInterface
{

    /**
     * @param  $message
     * @param  $level
     * @return string
     */
    public function addLog($message, $level)
    {
        //do code for writing logs to your files/databases
        //throw new Exception('my test');
        //return $x;
    }
}

class UserStorageTest implements UserStorageInterface
{

    /**
     * @param  $userId
     * @param  $campaignKey
     * @return string
     */
    public function get($userId, $campaignKey)
    {
        return [
            'userId' => $userId,
            'variationName' => 'Control',
            'campaignKey' => $campaignKey
        ];
    }

    /**
     * @param  $campaignInfo
     * @return bool
     */
    public function set($campaignInfo)
    {
        return true;
    }
}

/***
 * Class VWOTest
 *
 * @package vwo
 */
class VWOTest extends TestCase
{

    private $vwotest;
    private $settingsArr1 = '';
    private $settingsArr2 = '';
    private $settingsArr3 = '';
    private $settingsArr4 = '';
    private $settingsArr5 = '';
    private $settingsArr6 = '';
    private $settingsArr7 = '';
    private $settingsArr8 = '';
    private $variationResults = '';
    private $segmentEvaluatorJson = '';

    public function testGetSettings()
    {
        $accountId = '12345';
        $sdkKey = '1111111111111111111111';
        $result = VWO::getSettingsFile($accountId, $sdkKey);
        $expected = false;
        $obj = new VWO('');
        $config = [
            'settingsFile' => $this->settingsArr8,
            'isDevelopmentMode' => 0
        ];
        $obj = new VWO($config);
        $obj->activate('FEATURE_TEST', 'Ian');
        $obj->isFeatureEnabled('FEATURE_TEST', 'Ian');
        $obj->isFeatureEnabled(1234, 2342);
        $variationName = $obj->getVariationName('FEATURE_TEST', 'Ian');

        $this->assertEquals($expected, $result);
    }


    public function testIsFeatureEnabled()
    {
        for ($devtest = 8; $devtest < 9; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 0,
                'logging' => new CustomLogger(),

            ];
            $this->vwotest = new VWO($config);
            $featureName1 = 'FEATURE_TEST';
            $users = $this->getUsers();
            foreach ($users as $userId) {
                $isFeatureEnabled = $this->vwotest->isFeatureEnabled($featureName1, $userId);
                $variation = $this->vwotest->getVariationName($featureName1, $userId);
                $featureVariableValue = $this->vwotest->getFeatureVariableValue($featureName1, 'V1', $userId);
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
                $this->assertEquals($expectedIsFeatureEnabled, $isFeatureEnabled);
                $this->assertEquals($expectedFeatureVariableValue, $featureVariableValue);
            }
        }
    }

    public function testActivate()
    {
        for ($devtest = 1; $devtest < 7; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 1
            ];
            $this->vwotest = new VWO($config);
            $campaignKey = 'DEV_TEST_' . $devtest;
            $users = $this->getUsers();
            for ($i = 0; $i < count($users); $i++) {
                $userId = $users[$i];
                $variationName = $this->vwotest->activate($campaignKey, $userId);
                $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
                $this->assertEquals($expected, $variationName);
            }
        }
    }

    public function testGetVariation()
    {
        for ($devtest = 1; $devtest < 7; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 1
            ];
            $this->vwotest = new VWO($config);
            $campaignKey = 'DEV_TEST_' . $devtest;
            $users = $this->getUsers();
            for ($i = 0; $i < count($users); $i++) {
                $userId = $users[$i];
                $variationName = $this->vwotest->getVariationName($campaignKey, $userId);
                $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
                $this->assertEquals($expected, $variationName);
            }
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

    public function testTrack()
    {
        for ($devtest = 1; $devtest < 7; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 0
            ];
            $this->vwotest = new VWO($config);
            $campaignKey = 'DEV_TEST_' . $devtest;
            $users = $this->getUsers();
            $options = [];
            for ($i = 0; $i < 26; $i++) {
                $userId = $users[$i];
                foreach ($config['settingsFile']['campaigns'] as $campaign) {
                    if ($campaign['key'] == $campaignKey) {
                        $goalname = $campaign['goals'][0]['identifier'];
                        if ($campaign['goals'][0]['type'] == 'REVENUE_TRACKING') {
                            $options['revenueValue'] = 10;
                        }
                        break;
                    }
                }
                $result = $this->vwotest->track($campaignKey, $userId, $goalname, $options);
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

    public function testTrackForUser()
    {
        $setting = 'settingsArr8';
        $config = [
            'settingsFile' => $this->$setting,
            'isDevelopmentMode' => 0,
            'logging' => new CustomLogger(),
            'userStorageService' => new UserStorageTest()
        ];
        $this->vwotest = new VWO($config);
        $campaignKey = 'DEV_TEST_8';
        $users = $this->getUsers();
        $userId = $users[0];
        $goalname = 'dsa';
        $result = $this->vwotest->track($campaignKey, $userId, $goalname);
        $expected = ucfirst($this->variationResults[$campaignKey][$userId]);
        if ($expected == null) {
            $expected = false;
        } else {
            $expected = true;
        }
        $this->assertEquals($expected, $result);
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
        $config = [
            'settingsFile' => $whitelistingSetting,
            'isDevelopmentMode' => 0
        ];
        $customVariables = ['contains_vwo' => 'qqvwoqq', 'regex_for_all_letters' => 'abc', 'regex_for_small_letters' => 'www', 'regex_for_zeros' => 0, 'regex_for_capital_letters' => 'ABC', 'regex_for_no_zeros' => 123, 'regex_real_number' => 123, 'starts_with' => 'vwo'];
        $this->vwotest = new VWO($config);
        $variationName = $this->vwotest->getVariationName($data['camapaignKey'], $data['userId'], ['variationTargetingVariables' => $whitelistingTags, 'customVariables' => $customVariables]);
        $variationNameForFalse = $this->vwotest->getVariationName($data['camapaignKey'], $data['userId'], ['variationTargetingVariables' => $falseWhiteListingTags, 'customVariables' => $customVariables]);

        $expected1 = 'Variation-2';
        $expected2 = 'Control';
        $this->assertEquals($expected1, $variationName);
        $this->assertEquals($expected2, $variationNameForFalse);
    }

    public function testPushApi()
    {
        $userId = 'Ashley';
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

        $setting = 'settingsArr8';
        $config = [
            'settingsFile' => $this->$setting,
            'isDevelopmentMode' => 0,
            'logging' => new CustomLogger(),
            'userStorageService' => new UserStorageTest()
        ];
        $this->vwotest = new VWO($config);
        foreach ($cases as $case) {
            $response = $this->vwotest->push($case['tagKey'], $case['tagValue'], $userId);
            $this->assertEquals($case['expected'], $response);
        }
        $configWithdevMode = [
            'settingsFile' => $this->$setting,
            'isDevelopmentMode' => 1,
            'logging' => new CustomLogger(),
            'userStorageService' => new UserStorageTest()
        ];
        $this->vwotest = new VWO($configWithdevMode);
        $response = $this->vwotest->push($cases[0]['tagKey'], $cases[0]['tagValue'], $userId);
        $this->assertEquals($cases[0]['expected'], $response);
    }

    protected function setUp()
    {
        $settings1 = new Settings1();
        $settings2 = new Settings2();
        $settings3 = new Settings3();
        $settings4 = new Settings4();
        $settings5 = new Settings5();
        $settings6 = new Settings6();
        $settings7 = new Settings7();
        $settings8 = new Settings8();
        $segmentEvaluatorJson = new SegmentEvaluatorJson();
        $results = new VariationResults();

        $this->settingsArr1 = $settings1->setting;
        $this->settingsArr2 = $settings2->setting;
        $this->settingsArr3 = $settings3->setting;
        $this->settingsArr4 = $settings4->setting;
        $this->settingsArr5 = $settings5->setting;
        $this->settingsArr6 = $settings6->setting;
        $this->settingsArr7 = $settings7->setting;
        $this->settingsArr8 = $settings8->setting;
        $this->variationResults = $results->results;
        $this->segmentEvaluatorJson = $segmentEvaluatorJson->setting;
    }

    private function getUsers()
    {
        $users = [
            'Ashley',
            'Bill',
            'Chris',
            'Dominic',
            'Emma',
            'Faizan',
            'Gimmy',
            'Harry',
            'Ian',
            'John',
            'King',
            'Lisa',
            'Mona',
            'Nina',
            'Olivia',
            'Pete',
            'Queen',
            'Robert',
            'Sarah',
            'Tierra',
            'Una',
            'Varun',
            'Will',
            'Xin',
            'You',
            'Zeba'
        ];

        return $users;
    }

    public function testTrackValidation()
    {
        $setting = 'settingsArr8';
        $config = [
            'settingsFile' => $this->$setting,
            'isDevelopmentMode' => 0,
            'logging' => new CustomLogger(),
            'userStorageService' => new UserStorageTest()
        ];
        $this->vwotest = new VWO($config);
        $resultForInvalidParams = $this->vwotest->track();
        $resultForRollout = $this->vwotest->track('FEATURE_ROLLOUT', 'Xin', 'CUSTOM_GOAL');
        $this->assertEquals(false, $resultForInvalidParams);
        $this->assertEquals(false, $resultForRollout);
    }

    public function testCampaignUtilError()
    {
        $x = 0;
        try {
            // used try catch as expection for this test case is
            // execption and for phpunit exception is test case failure
            Campaign::makeRanges('');
        } catch (\Exception $e) {
            $x = 1;
        }
        $this->assertEquals(1, $x);
    }

    public function testValidatePushApiParams()
    {
        $tagValue = 'qwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTest';
        $tagValue .= 'qwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTest';
        $tagValue .= 'qwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTestqwertyTest';
        $useridError = Validations::pushApiParams('abc', '', '');
        $tagValueEmpty = Validations::pushApiParams('abc', '', 'Xin');
        $tagValueSizeError = Validations::pushApiParams('abc', $tagValue, 'Xin');
        $this->assertEquals(false, $useridError);
        $this->assertEquals(false, $tagValueEmpty);
        $this->assertEquals(false, $tagValueSizeError);
    }

    public function testValidateCampaignKey()
    {
        $campaignKeyError = Validations::validateIsFeatureEnabledParams(1, 1);
        $checkSettingSchema = Validations::checkSettingSchema('');
        $invalidUserId = Validations::validateUserId(0);
        $this->assertEquals(false, $invalidUserId);
        $this->assertEquals(false, $campaignKeyError);
        $this->assertEquals(false, $checkSettingSchema);
    }

    public function testCheckPreSegmentation()
    {
        $segment = json_decode('{"dsl":{"and":[{"custom_variable":{"eq":"eq_value"}}]},"expectation":true,"customVariables":{"eq":"eq_value"}}', 1);
        $camapign = [
            'key' => 'test',
            'segments' => $segment['dsl']
        ];
        $options = ['customVariables' => $segment['customVariables']];
        $res = Validations::checkPreSegmentation($camapign, 'Xin', $options);
        $this->assertEquals(true, $res);
    }
}
