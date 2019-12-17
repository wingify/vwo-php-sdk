<?php

/**
 * Copyright 2019 Wingify Software Pvt. Ltd.
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

use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Exception;
use vwo\Storage\UserStorageInterface;
use vwo\Logger\LoggerInterface;
use vwo\Utils\SegmentEvaluator;
use vwo\Utils\OperandEvaluator;

/**
 * Class CustomLogger
 */
class CustomLogger implements LoggerInterface
{

    /**
     * @param $message
     * @param $level
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
     * @param $userId
     * @param $campaignName
     * @return string
     */
    public function get($userId, $campaignName)
    {
        return [
            'userId' => $userId,
            'variationName' => 'Control',
            'campaignName' => $campaignName
        ];
    }

    /**
     * @param $campaignInfo
     * @return bool
     */
    public function set($campaignInfo)
    {
        return true;
    }
}

/***
 * Class VWOTest
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
            $featureName2 = 'FEATURE_ROLLOUT_ONLY';
            $users = $this->getUsers();
            foreach ($users as $userId) {
                try {
                    $variationName = $this->vwotest->isFeatureEnabled($featureName1, $userId);
                    $variableValue = $this->vwotest->getFeatureVariableValue($featureName1, 'V1', $userId);
                    //$expected=ucfirst($this->variationResults[$campaignName][$userId]);
                    $expected = $variableValue;
                    $this->assertEquals($expected, $variationName);
                    // running only one test case
                    break;
                } catch (Exception $e) {
                }
            }
        }
    }

    public function testActivate()
    {
        for ($devtest = 8; $devtest < 9; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 1
            ];
            $this->vwotest = new VWO($config);
            $campaignName = 'DEV_TEST_' . $devtest;
            $users = $this->getUsers();
            for ($i = 0; $i < 1; $i++) {
                try {
                    $userId = $users[$i];
                    $variationName = $this->vwotest->activate($campaignName, $userId);
                    $expected = ucfirst($this->variationResults[$campaignName][$userId]);
                    $this->assertEquals($expected, $variationName);
                } catch (Exception $e) {
                }
            }
        }
    }

    public function testGetVariation()
    {
        for ($devtest = 8; $devtest < 9; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 1
            ];
            $this->vwotest = new VWO($config);
            $campaignName = 'DEV_TEST_' . $devtest;
            $users = $this->getUsers();
            for ($i = 0; $i < 26; $i++) {
                try {
                    $userId = $users[$i];
                    $variationName = $this->vwotest->getVariationName($campaignName, $userId);
                    $expected = ucfirst($this->variationResults[$campaignName][$userId]);
                    $this->assertEquals($expected, $variationName);
                    break;
                } catch (Exception $e) {
                }
            }
        }
    }
    public function testSegmentEvaluator()
    {
        $segmentData = json_decode(str_replace('\\', '\\\\', $this->segmentEvaluatorJson), 1);
        foreach ($segmentData as $key => $segments) {
            foreach ($segments as $segment) {
                $segmentObj = new SegmentEvaluator();
                if (($key == 'starts_with_operand_tests' || $key == 'ends_with_operand_tests' || $key = 'contains_operand') && $segment['description'] == 'boolean_data_type') {
                    continue;
                }
                $res = $segmentObj->evaluate($segment['dsl'], $segment['tags']);
                $this->assertEquals($segment['expectation'], $res);
            }
        }
    }

    public function testTrack()
    {
        for ($devtest = 8; $devtest < 9; $devtest++) {
            $setting = 'settingsArr' . $devtest;
            $config = [
                'settingsFile' => $this->$setting,
                'isDevelopmentMode' => 0
            ];
            $this->vwotest = new VWO($config);
            $campaignName = 'DEV_TEST_' . $devtest;
            $users = $this->getUsers();
            for ($i = 0; $i < 26; $i++) {
                try {
                    $userId = $users[$i];
                    $goalname = $config['settingsFile']['campaigns'][2]['goals'][0]['identifier'];
                    $result = $this->vwotest->track($campaignName, $userId, $goalname);
                    $expected = ucfirst($this->variationResults[$campaignName][$userId]);
                    if ($expected == null) {
                        $expected = false;
                    } else {
                        $expected = true;
                    }
                    $this->assertEquals($expected, $result);
                    break;
                } catch (Exception $e) {
                }
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
        $campaignName = 'DEV_TEST_8';
        $users = $this->getUsers();
        $userId = $users[0];
        $goalname = 'dsa';
        $result = $this->vwotest->track($campaignName, $userId, $goalname);
        $expected = ucfirst($this->variationResults[$campaignName][$userId]);
        if ($expected == null) {
            $expected = false;
        } else {
            $expected = true;
        }
        $this->assertEquals($expected, $result);
    }

    public function testPushApi()
    {
        $userId = 'Ashley';
        $cases = [
            //empty case
            ['tagKey' => '','tagValue' => '','expected' => false],
            //length check
            ['tagKey' => 'qwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuiopptyeytry',
                'tagValue' => 'qwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuioppqwertyuiopptyeytry',
                'expected' => false],
            //datatype case
            ['tagKey' => 1,'tagValue' => 2,'expected' => false],
            //happy case
            ['tagKey' => 'foo','tagValue' => 'bar','expected' => true],
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






    /***
     * @return mixed
     */
    private function getRandomUser()
    {
        $users = $this->getUsers();

        return $users[rand(0, 25)];
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
}
