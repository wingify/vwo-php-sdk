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

class MutuallyExclusiveTest extends TestCase
{
    protected function setUp()
    {
        $this->settingsFileMEG = SettingsFileMEG::setup();
    }

    public function testVariationReturnAsWhitelisting()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isDevelopmentMode' => 1]);
        $options = [
            'variationTargetingVariables' => [
            'chrome' => false
            ]
        ];
        // called campaign satisfies the whitelisting
        $variation = $vwoInstance->activate($campaignKey, 'Ashley', $options);
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM', $options);
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley', $options);
        $this->assertEquals($variation, 'Variation-1');
        $this->assertEquals($variationName, 'Variation-1');
        $this->assertEquals($isGoalTracked, true);
    }

    public function testNullVariationAsOtherCampaignSatisfiesWhitelisting()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][3]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG);
        $options = [
            'variationTargetingVariables' => [
                'chrome' => false
            ]
        ];

        $variation = $vwoInstance->activate($campaignKey, 'Ashley', $options);
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM', $options);
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley', $options);
        $this->assertEquals($variation, null);
        $this->assertEquals($variationName, false);
        $this->assertEquals($isGoalTracked, null);
    }

    public function testVariationForCalledCampaign()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isUserStorage' => 1, 'isDevelopmentMode' => 1]);

        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM');
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley');
        $this->assertEquals($variation, 'Control');
        $this->assertEquals($variationName, 'Control');
        $this->assertEquals($isGoalTracked, true);
    }

    public function testNullVariationAsOtherCampaignSatisfiesStorage()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isDevelopmentMode' => 1]);

        $variationInfo = [
            'userId' => 'Ashley',
            'variationName' => 'Control',
            'campaignKey' => $campaignKey
        ];
        $vwoInstance->_userStorageObj = TestUtil::mockUserStorageInterface($this, $variationInfo);

        // called campaign satisfies the whitelisting
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley');
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM');
        $this->assertEquals($variation, 'Control');
        $this->assertEquals($variationName, 'Control');
        $this->assertEquals($isGoalTracked, true);

        $campaignKey = $this->settingsFileMEG['campaigns'][3]['key'];
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley');
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM');
        $this->assertEquals($variation, null);
        $this->assertEquals($variationName, null);
        $this->assertEquals($isGoalTracked, null);
    }

    public function testVariationForCalledCampaignInStorageAndOtherCampaignSatisfiesWhitelisting()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isUserStorage' => 1]);

        $options = [
            'variationTargetingVariables' => [
                'browser' => "chrome"
            ]
        ];

        $segmentPassed = [
            "or" => [
                [
                    "custom_variable" => [
                        'browser' => "chrome"
                    ]
                ]
            ]
        ];

        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $this->assertEquals($variation, 'Control');
        $vwoInstance->settings['campaigns'][3]['segments'] = $segmentPassed;
        $variation = $vwoInstance->activate($campaignKey, 'Ashley', $options);
        $this->assertEquals($variation, 'Control');
    }

    public function testNullVariationWhenCampaignNotInGroup()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][4]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG);

        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley');
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM');
        $this->assertEquals($variation, null);
        $this->assertEquals($variationName, null);
        $this->assertEquals($isGoalTracked, null);
    }

    public function testNoCampaignsSatisfiesPresegmentation()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][0]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG);

        $options = [
            'customVariables' => [
                'browser' => "chrome"
            ]
        ];

        $segmentPassed = [
            "or" => [
                [
                    "custom_variable" => [
                        'chrome' => "false"
                    ]
                ]
            ]
        ];

        $vwoInstance->settings['campaigns'][0]['segments'] = $segmentPassed;
        $vwoInstance->settings['campaigns'][1]['segments'] = $segmentPassed;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley', $options);
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley', $options);
        $this->assertEquals($variation, null);
        $this->assertEquals($variableValue, false);

        // implementing the same condition with zero traffic percentage
        $vwoInstance->settings['campaigns'][0]['percentTraffic'] = 0;
        $vwoInstance->settings['campaigns'][1]['percentTraffic'] = 0;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley', $options);
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley', $options);
        $this->assertEquals($variation, null);
        $this->assertEquals($variableValue, false);
    }

    public function testCalledCampaignNotSatisfyingPresegmentation()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][0]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG);

        $options = [
            'customVariables' => [
                'browser' => "chrome"
            ]
        ];

        $segmentFailed = [
            "or" => [
                [
                    "custom_variable" => [
                        'chrome' => "false"
                    ]
                ]
            ]
        ];

        $segmentPassed = [
            "or" => [
                [
                    "custom_variable" => [
                        "browser" => 'chrome'
                    ]
                ]
            ]
        ];

        $vwoInstance->settings['campaigns'][0]['segments'] = $segmentFailed;
        $vwoInstance->settings['campaigns'][1]['segments'] = $segmentPassed;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley', $options);
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley', $options);
        $this->assertEquals($variation, null);
        $this->assertEquals($variableValue, false);

        // implementing the same condition with with different traffic distribution
        $vwoInstance->settings['campaigns'][0]['percentTraffic'] = 0;
        $vwoInstance->settings['campaigns'][1]['percentTraffic'] = 100;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley', $options);
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley', $options);
        $this->assertEquals($variation, null);
        $this->assertEquals($variableValue, false);
    }

    public function testOnlyCalledCampaignSatisfyPresegmentation()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][0]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isDevelopmentMode' => 1]);

        $options = [
            'customVariables' => [
                'browser' => "chrome"
            ]
        ];

        $segmentFailed = [
            "or" => [
                [
                    "custom_variable" => [
                        'chrome' => "false"
                    ]
                ]
            ]
        ];

        $segmentPassed = [
            "or" => [
                [
                    "custom_variable" => [
                        "browser" => 'chrome'
                    ]
                ]
            ]
        ];

        $vwoInstance->settings['campaigns'][0]['segments'] = $segmentPassed;
        $vwoInstance->settings['campaigns'][1]['segments'] = $segmentFailed;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley', $options);
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley', $options);
        $this->assertEquals($variation, true);
        $this->assertEquals($variableValue, 'Control string');

        // implementing the same condition with with different traffic distribution
        $vwoInstance->settings['campaigns'][0]['percentTraffic'] = 100;
        $vwoInstance->settings['campaigns'][1]['percentTraffic'] = 0;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley', $options);
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley', $options);
        $this->assertEquals($variation, true);
        $this->assertEquals($variableValue, 'Control string');
    }

    public function testCalledCampaignWinnerCampaign()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][0]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isDevelopmentMode' => 1]);

        // implementing the same condition with different traffic distribution
        $vwoInstance->settings['campaigns'][0]['percentTraffic'] = 100;
        $vwoInstance->settings['campaigns'][1]['percentTraffic'] = 100;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'Ashley');
        $variableValue = $vwoInstance->getFeatureVariableValue($campaignKey, 'STRING_VARIABLE', 'Ashley');
        $this->assertEquals($variation, true);
        $this->assertEquals($variableValue, 'Control string');

        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $variationName = $vwoInstance->getVariationName($campaignKey, 'Ashley');
        $isGoalTracked = $vwoInstance->track($campaignKey, 'Ashley', 'CUSTOM');
        $this->assertEquals($variation, 'Control');
        $this->assertEquals($variationName, 'Control');
        $this->assertEquals($isGoalTracked, true);
    }

    public function testCalledCampaignNotWinnerCampaign()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][0]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG);

        // implementing the same condition with same traffic distribution
        $vwoInstance->settings['campaigns'][0]['percentTraffic'] = 100;
        $vwoInstance->settings['campaigns'][1]['percentTraffic'] = 100;
        $variation = $vwoInstance->isFeatureEnabled($campaignKey, 'lisa');
        $this->assertEquals($variation, false);

        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $variation = $vwoInstance->activate($campaignKey, 'lisa');
        $this->assertEquals($variation, null);
    }

    public function testWhenEqualTrafficAmongEligibleCampaigns()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isDevelopmentMode' => 1]);

        // implementing the same condition with different traffic distribution
        $vwoInstance->settings['campaigns'][2]['percentTraffic'] = 80;
        $vwoInstance->settings['campaigns'][3]['percentTraffic'] = 50;
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $this->assertEquals($variation, 'Variation-1');
    }

    public function testWhenBothCampaignsNewToUser()
    {
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isDevelopmentMode' => 1]);

        // campaigns are newly added to MEG.
        // user could be a part of any one of the campaign.
        $variation = $vwoInstance->activate($this->settingsFileMEG['campaigns'][2]['key'], 'Ashley');
        $this->assertEquals($variation, 'Control');
        $variation = $vwoInstance->activate($this->settingsFileMEG['campaigns'][3]['key'], 'Ashley');
        $this->assertEquals($variation, null);
    }

    public function testWhenUserAlreadyPartOfCampaignAndNewCampaignAddedToGroup()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG);

        $variationInfo = [
            'userId' => 'Ashley',
            'variationName' => 'Control',
            'campaignKey' => $campaignKey
        ];
        $vwoInstance->_userStorageObj = TestUtil::mockUserStorageInterface($this, $variationInfo);

        // user is already a part of a campaign
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $this->assertEquals($variation, 'Control');

        // new campaign is added to the group
        $vwoInstance->settings['campaignGroups'][164] = 2;
        $vwoInstance->settings['groups'][2]['campaigns'][] = 164;
        $variation = $vwoInstance->activate($this->settingsFileMEG['campaigns'][4]['key'], 'Ashley');
        $this->assertEquals($variation, null);
    }

    public function testWhenViewedCampaignRemovedFromGroup()
    {
        $campaignKey = $this->settingsFileMEG['campaigns'][2]['key'];
        $vwoInstance = TestUtil::instantiateSdk($this->settingsFileMEG, ['isUserStorage' => 1]);

        $variationInfo = [
            'userId' => 'Ashley',
            'variationName' => 'Control',
            'campaignKey' => $campaignKey
        ];
        $vwoInstance->_userStorageObj = TestUtil::mockUserStorageInterface($this, $variationInfo);

        // user is already a part of a campaign
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $this->assertEquals($variation, 'Control');

        // old campaign is removed from the group
        $vwoInstance->settings['groups'][2]['campaigns'] = [163];

        // since user has already seen that campaign, they will continue to become part of that campaign
        $variation = $vwoInstance->activate($campaignKey, 'Ashley');
        $this->assertEquals($variation, 'Control');
    }
}
