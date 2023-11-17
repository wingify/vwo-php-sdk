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
use vwo\Core\VariationDecider as VariationDecider;

class BucketerTest extends TestCase
{
    private $variationDecider;
    protected function setUp(): void
    {
        $this->users = TestUtil::getUsers();
        $this->variationDecider = new VariationDecider();

        $this->settingsWithSegments = new SettingsWithPreSegmentation();

        $this->campaign = $this->settingsWithSegments->setting['campaigns'][0];

        $this->settingsFileBucketing = new SettingsFileBucketing();
        $this->variationResults = new VariationResults();
        

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
            $result = Bucketer::getBucket($userId, $campaign, false);

            $this->assertEquals(true, is_string($result['name']));
            $this->assertEquals(true, $result['name'] === 'Control' || $result['name'] === 'Variation-1');
        }
    }

    public function testGetBucketWithoutVariationRanges()
    {
        foreach ($this->users as $userId) {
            $result = Bucketer::getBucket($userId, $this->campaign, false);
            $this->assertEquals(true, is_null($result));
        }
    }

    public function testGetBucketValue()
    {
        $campaign = ["id" => 1, "isBucketingSeedEnabled" => true];
        list($bucketValue, $hashValue) = Bucketer::getBucketVal("someone@mail.com", $campaign, false);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 2444);

        $campaign["isBucketingSeedEnabled"] = false;
        list($bucketValue, $hashValue) = Bucketer::getBucketVal("someone@mail.com", $campaign, false);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 6361);
    }

    public function testGetBucketValueForUser1111111111111111()
    {
        $campaign = ["id" => 1, "isBucketingSeedEnabled" => true];
        list($bucketValue, $hashValue) = Bucketer::getBucketVal("1111111111111111", $campaign, false);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 8177);

        $campaign["isBucketingSeedEnabled"] = false;
        list($bucketValue, $hashValue) = Bucketer::getBucketVal("1111111111111111", $campaign, false);
        $bucketValue = Bucketer::getRangeForVariations($bucketValue);
        $this->assertEquals($bucketValue, 4987);
    }

    public function testWithNewBucketingLogicAndSeedNotEnabled()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_without_seed_and_without_isOB;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaignKey = $settingsFile['campaigns'][0]['key'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITHOUT_SEED'];

        foreach($this->users as $i => $userId){
            $result = $vwoInstance->activate($campaignKey, $userId);
            $this->assertEquals($expected[$userId], $result);
        }
    }

   
    public function testWithNewBucketingLogicAndSeedEnabled()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_with_seed_and_without_isOB;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaignKey = $settingsFile['campaigns'][0]['key'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITH_SEED'];
        
        foreach($this->users as $i => $userId){
            $result = $vwoInstance->activate($campaignKey, $userId);
            $this->assertEquals($expected[$userId], $result);
        }
    }

    
    public function testWithIsNBAndisOBWithOldBucketingLogic()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_with_isNB_and_with_isOB;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaignKey = $settingsFile['campaigns'][0]['key'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITH_SEED_WITH_isNB_WITH_isOB'];

        foreach($this->users as $i => $userId){
            $result = $vwoInstance->activate($campaignKey, $userId);
            $this->assertEquals($expected[$userId], $result);
        }
    }
    
    public function testWithIsNBAndNewBucketingLogicWithoutIsOB()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_with_isNB_and_without_isOB;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaignKey = $settingsFile['campaigns'][0]['key'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITH_SEED_WITH_isNB_WITHOUT_isOB'];


        foreach($this->users as $i => $userId){
            $result = $vwoInstance->activate($campaignKey, $userId);
            $this->assertEquals($expected[$userId], $result);
        }
    }

    public function testWithIsNBAndNewBucketingLogicWithoutIsOBAndSeedFlag()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_with_isNB_and_without_isOB_and_without_seed_flag;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaignKey = $settingsFile['campaigns'][0]['key'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITHOUT_SEED_FLAG_WITH_isNB_WITHOUT_isOB'];

        foreach($this->users as $i => $userId){
            $result = $vwoInstance->activate($campaignKey, $userId);
            $this->assertEquals($expected[$userId], $result);
        }
    }

    public function testSameUserMultipleCampaignsWithIsNBv2AndWithoutIsOBv2()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_with_isNBv2_and_without_isOBv2_and_without_seed_flag;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaigns = $settingsFile['campaigns'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITHOUT_SEED_FLAG_WITH_isNBV2_WITHOUT_isOBV2'];

        foreach($campaigns as $i => $campaign){
            $result[] = $vwoInstance->activate($campaign['key'], 'Ashley');
        }
        $this->assertEquals($expected, $result);
    }

    public function testSameUserMultipleCampaignsWithIsNBv2AndIsOBv2()
    {
        // initializations
        $settings = new SettingsFileBucketing();
        $settingsFile = $settings->setting_with_isNBv2_and_with_isOBv2_and_without_seed_flag;
        $vwoInstance = TestUtil::instantiateSdk($settingsFile, ['isDevelopmentMode' => 1]);
        $campaigns = $settingsFile['campaigns'];

        //expected result array
        $expected = $this->variationResults->results['BUCKET_ALGO_WITHOUT_SEED_FLAG_WITH_isNBV2_WITH_isOBV2'];

        foreach($campaigns as $i => $campaign){
            $result[] = $vwoInstance->activate($campaign['key'], 'Ashley');
        }
        $this->assertEquals($expected, $result);
    }
}
