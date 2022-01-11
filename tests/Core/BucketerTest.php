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
use vwo\Core\Bucketer as Bucketer;
use vwo\Utils\Campaign as CampaignUtil;

class BucketerTest extends TestCase
{
    protected function setUp()
    {
        $this->users = TestUtil::getUsers();

        $this->settingsWithSegments = new SettingsWithPreSegmentation();

        $this->campaign = $this->settingsWithSegments->setting['campaigns'][0];
    }

    public function testGetBucketVariationId()
    {
        $result = Bucketer::getBucketVariationId($this->campaign, 'no-matching-variaition-name');
        $this->assertEquals(true, is_null($result));
    }

    public function testGetBucketWithVariationRanges()
    {
        $settingsFile = CampaignUtil::makeRanges($this->settingsWithSegments->setting);
        $campaign = $settingsFile['campaigns'][0];

        foreach ($this->users as $userId) {
            $result = Bucketer::getBucket($userId, $campaign);

            $this->assertEquals(true, is_string($result['name']));
            $this->assertEquals(true, $result['name'] === 'Control' || $result['name'] === 'Variation-1');
        }
    }

    public function testGetBucketWithoutVariationRanges()
    {
        foreach ($this->users as $userId) {
            $result = Bucketer::getBucket($userId, $this->campaign);
            $this->assertEquals(true, is_null($result));
        }
    }

    public function testGetBucketValue()
    {
        $campaign = ["id" => 1, "isBucketingSeedEnabled" => true];
        $bucketValue = Bucketer::getBucketVal("someone@mail.com", $campaign);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 2444);

        $campaign["isBucketingSeedEnabled"] = false;
        $bucketValue = Bucketer::getBucketVal("someone@mail.com", $campaign);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 6361);
    }

    public function testGetBucketValueForUser1111111111111111()
    {
        $campaign = ["id" => 1, "isBucketingSeedEnabled" => true];
        $bucketValue = Bucketer::getBucketVal("1111111111111111", $campaign);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 8177);

        $campaign["isBucketingSeedEnabled"] = false;
        $bucketValue = Bucketer::getBucketVal("1111111111111111", $campaign);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 4987);
    }

}
