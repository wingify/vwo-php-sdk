# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.11.0] - 2020-01-14

### Changed

- Make VWO tracking calls asynchronous by using socket connection. This reduces the overall execution time of tracking APIs - `activate`, `isFeatureEnabled`, `track`, and `push` as the SDK is not blocked on the response of such tracking calls.

## [1.10.0] - 2020-01-13

### Added
- Webhooks support. Updated `getSettingsFile` API to fetch latest settings-file when webhook is triggered.

## [1.8.0] - 2020-05-30
### Changed
- Refactor entire SDK - into modular and reusable components
- Improve Test Cases - cleaner & structured code and reusable utitlity functions
- Coverage increased from 83% to ~96% i.e. covering most of the source code via E2E and Unit Tests
- Bugfixes
  - pre-segmentation logging issue when evaluated to false
  - sending call in case of feature-test when Control is the decision

## [1.7.4] - 2020-05-14
### Changed
- Return `null` from APIs in case user is not part of campaign

## [1.7.3] - 2020-05-12
### Changed
- Fixed and added new test cases for feature rollout and validation checks
- Mock server-calls, thereby, test-cases execution time reduced from ~1.5 minutes to only ~500ms

## [1.7.2] - 2020-05-06
### Changed
- Fix variable naming and update `README.md` file

## [1.7.1] - 2020-04-29
### Changed
- Use `bucketinfo` correctly in `VWO.php` which was causing weird issues related to decision making in Feature Rollout and Feature Test.

## [1.7.0] - 2020-04-28
### Changed
- Updated monolog dependency to allow 2.x versions if php >= 7.2 version is used

## [1.6.1] - 2020-04-13
### Changed
- Updated monolog dependency to allow newer versions to be downloaded in update
- Added `composer.lock` into `.gitignore` file.

## [1.6.0] - 2020-03-05
### Breaking Changes
To prevent ordered arguments and increasing use-cases, we are moving all optional arguments into a combined argument(Associative Array).

- customVariables argument in APIs: `activate`, `getVariation`, `track`, `isFeatureEnabled`, and `getFeatureVariableValue` have been moved into `options`.
- `revenueValue` parameter in `track` API is now moved into `options` argument.

#### Before

```php
// activae API
$vwoClient->activate($campaignKey, $userId, $customVariables);
// getVariation API
$vwoClient->getVariation($campaignKey, $userId, $customVariables);
// track API
$vwoClient->track($campaignKey, $userId, $goalIdentifier, $revenueValue, $customVariables);
// isFeatureEnabled API
$vwoClient->isFeatureEnabled($campaignKey, $userId, $customVariables);
// getFeatureVariableValue API
$vwoClient->getFeatureVariableValue($campaignKey, $variableKey, $userId, $customVariables);
```

#### After

```php

$options = [];
$options["customVariables"] = [];
  // Optional, neeeded for Forced Variation
$options["variationTargetingVariables"] = [];

// activae API
$vwoClient->activate($campaignKey, $userId, $options);
// getVariation API
$vwoClient->getVariation($campaignKey, $userId, $options);
// track API
  // Optional, needed to track revenue goal with revenue value
$options["revenueValue"] = 1000.12;
$vwoClient->track($campaignKey, $userId, $goalIdentifier, options);
// isFeatureEnabled API
$vwoClient->isFeatureEnabled($campaignKey, $userId, $options);
// getFeatureVariableValue API
$vwoClient->getFeatureVariableValue($campaignKey, $variableKey, $userId, $options);
```

### Added
Forced Variation capabilites
- Introduced `Forced Variation` to force certain users into specific variation. Forcing can be based on User IDs or custom variables defined.
### Changed
- All existing APIs to handle variation-targeting-variables as an option for forcing variation
- Code refactored to support Whitelisting.

## [1.5.2] - 2020-01-24
### Changed
- Update endpoint to use `https` protocol instead of `http`
- Update UserStorage `set` function to properly validate user-data structure before setting

## [1.5.1] - 2020-01-23
### Changed
- Move "squizlabs/php_codesniffer" from dependencies to dev-dependencies in `composer.json`
- Add `.gitattributes` file for smaller downloads i.e. only required files and folders when downloaded

## [1.5.0] - 2019-12-17
### Added
- Feature Rollout and Feature Test capabilities
- Pre and Post segmentation capabilites
  Introduced new Segmentation service to evaluate whether user is eligible for campaign based on campaign pre-segmentation conditions and passed custom-variables
### Changed
- Existing APIs to handle new type of campaigns i.e. feature-rollout and feature-test
- All existing APIs to handle custom-variables for tageting audience
- Code refactored to support feature-rollout, feature-test, campaign tageting and post segmentation

## [1.3.0] - 2019-11-21
### Changed
- Change MIT License to Apache-2.0
- Added apache copyright-header in each file
- Add NOTICE.txt file complying with Apache LICENSE
- Give attribution to the third-party libraries being used and mention StackOverflow

## [1.0.4] - 2019-11-01
### Changed
- Fix: Update schema validation when no campaigns are running

## [1.0.3] - 2019-10-13
### Changed
- Fix: show error log if goal is revenue type but no revenue value is passed in `track` API

## [1.0.2] - 2019-08-12
### Added
- Added more unit test cases
### Changed
- Update params that are sent to VWO server when track API is used

## [1.0.1] - 2019-07-32
### Changed
- Added `.travis.yml` file for running automated test cases on supported PHP versions

## [1.0.0] - 2019-07-30
### Added
- First release with Server-side A/B capabilities
