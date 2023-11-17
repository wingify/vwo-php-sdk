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
use Exception as Exception;
use vwo\Core\VariationDecider as VariationDecider;
use vwo\Services\HooksManager;
use vwo\Utils\Campaign;

class VariationDeciderTest extends TestCase
{
    /** @var VariationDecider $variationDecider */
    private $variationDecider;
    protected function setUp(): void
    {
        $this->users = TestUtil::getUsers();
        $this->variationDecider = new VariationDecider();
        $this->variationDecider->setHooksManager(new HooksManager([]));

        $settingsWithSegments = new SettingsWithPreSegmentation();
        $this->campaignWithSegments = $settingsWithSegments->setting['campaigns'][0];

        $this->variationResults = new VariationResults();
    }

    public function testFetchVariationData()
    {

        // if campaign is null
        foreach ($this->users as $userId) {
            $result = $this->variationDecider->fetchVariationData('', null, $userId, [], 'testcases');
            $this->assertEquals(true, is_null($result));
        }

        foreach ($this->users as $userId) {
            $result = $this->variationDecider->fetchVariationData('', $this->campaignWithSegments, $userId, [], 'testcases');
            $this->assertEquals(true, is_null($result));
        }
    }

    public function testUserStorageGet()
    {
        // when no data is available
        foreach ($this->users as $userId) {
            $result = TestUtil::invokePrivateMethod($this->variationDecider, 'userStorageGet', array( '', $userId, $this->campaignWithSegments ));
            $this->assertEquals(true, is_null($result));

            $result = TestUtil::invokePrivateMethod($this->variationDecider, 'userStorageGet', array( new UserStorageTest(), $userId, $this->campaignWithSegments ));

            $this->assertEquals(true, is_string($result['name']));
            $this->assertEquals('Control', $result['name']);

            $result = TestUtil::invokePrivateMethod($this->variationDecider, 'userStorageGet', array( new UserStorageGetCorruptedTest(), $userId, $this->campaignWithSegments ));
            $this->assertEquals(true, is_null($result));
        }
    }

    public function testUserStorageSet()
    {
        // when no data is available
        foreach ($this->users as $userId) {
            $result = TestUtil::invokePrivateMethod($this->variationDecider, 'userStorageSet', array( '', $userId, 'RANDOM', ['name' => 'Variation-1'] ));
            $this->assertEquals(true, is_null($result));

            $result = TestUtil::invokePrivateMethod($this->variationDecider, 'userStorageSet', array( new UserStorageTest(), $userId, 'RANDOM', ['name' => 'Variation-1'] ));
            $this->assertEquals(true, is_null($result));
        }
    }

    public function testVariationDataForRealTimePreSegmentation()
    {
        $setting = Campaign::makeRanges((new SettingsWithPreSegmentation())->setting);
        $campaign = $setting['campaigns'][0];
        $campaign['isAlwaysCheckSegment'] = true;

        $expected = $this->variationResults->results['REAL_TIME_PRE_SEGMENTATION'];

        foreach ($this->users as $i => $userId) {
            /** @var VariationDecider $variationDecider */
            $result = $this->variationDecider->fetchVariationData('', $campaign, $userId, ['customVariables' => ['browser' => 'chrome']], 'testcases');
            $this->assertEquals($expected[$userId], $result['name']);
        }
    }
}
