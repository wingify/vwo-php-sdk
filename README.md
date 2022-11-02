# VWO PHP SDK

[![Latest Stable Version](https://img.shields.io/packagist/v/vwo/vwo-php-sdk.svg)](https://packagist.org/packages/vwo/vwo-php-sdk) [![CI](https://github.com/wingify/vwo-php-sdk/workflows/CI/badge.svg?branch=master)](https://github.com/wingify/vwo-php-sdk/actions?query=workflow%3ACI)
 [![Coverage Status](https://coveralls.io/repos/github/wingify/vwo-php-sdk/badge.svg?branch=master)](https://coveralls.io/github/wingify/vwo-php-sdk?branch=master)[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0)

This open source library provides you server-side testing capabilities.

## Requirements

> PHP >= 5.6

## Installation

Install the latest version with

```bash
composer require vwo/vwo-php-sdk
```

## Basic Usage

**Use the below code for inital setup.**

```php
<?php
require_once('vendor/autoload.php');
require_once('userStorage.php'); // Optional :if you are using UserStorage service feature
require_once('customLogger.php');// Optional :if you are using custom logging feature

use vwo\VWO;

$accountId = REPLACE_WITH_VWO_ACCOUNT_ID; // eg: 123456
$sdkKey = 'REPLACE_WITH_TOKEN'; // eg: can be found in VWO app - FullStack project
$campaignKey = 'CAMPAIGN_UNIQUE_TEST_KEY';
$userId = 'USER_ID';
$goalIdentifier = 'REPLACE_WITH_CAMPAIGN_GOAL_IDENTIFIER';

// to fetch the settings i.e campaigns, variations and goals
$settingsFile = VWO::getSettingsFile($accountId, $sdkKey);

$sdkConfig = [
  'settingsFile' => $settingsFile,
  'isDevelopmentMode' => 0,  // optional: 1 to enable the dev mode
  'logging' => new CustomLogger(), // optional
  'userStorageService' => new userStorageService() // optional
];

$vwoClient = new VWO($sdkConfig);

// to get the variation name along with add a visitor hit to vwo app stats
$variation = $vwoClient->activate($campaignKey, $userId, $options);
// Or, to get the variation name
$variation = $vwoClient->getVariationName($campaignKey, $userId, $options);

// add code here to use variation
//...

/**
*send the track api hit to the vwo app stats to increase conversions
* $revenue is optional send in case if there is any revenue inside $options
*/

$vwoClient->track($campaignKey, $userId, $goalIdentifier, $options);
```

**Code for implementing User Storage Service**

```php
<?php
require_once('vendor/autoload.php');
use vwo\Utils\UserStorageInterface;
Class UserStorage implements UserStorageInterface{
  /**
    * @param $userId
    * @param $campaignKey
    *
    * @return array
    */
  public function get($userId, $campaignKey) {
    // search in DB/Storage system
    $variation = $db->fetch($userId, $campaignKey); // replace with your implementation

    return[
      'userId' => $userId,
      'campaignKey' => $campaignKey,
      'variationName' => $variation
    ];
  }

  /**
    * @param $campaignUserMapping
    * @return bool - could be changed
    */
  public function set($campaignUserMapping) {
    // S...code to store in DB/storage system
    return True;
  }
}
```

**Code for implementing Custom Logger**

```php
<?php
require_once('vendor/autoload.php');
use vwo\Logger\LoggerInterface;

/**
 * Class CustomLogger
 */
Class CustomLogger implements LoggerInterface{
  /**
    * @param $message
    * @param $level
    *
    * @return
    */
  public function log($message, $level){
    // use $level and log $message to either print or store them for later use
  }

}
```

## Documentation

Refer [Official VWO Documentation](https://developers.vwo.com/reference#fullstack-introduction)

## Third-party Resources and Credits

Refer [third-party-attributions.txt](https://github.com/wingify/vwo-php-sdk/blob/master/third-party-attributions.txt)

## Changelog

Refer [CHANGELOG.md](https://github.com/wingify/vwo-php-sdk/blob/master/CHANGELOG.md)

## Development and Test Cases

1. Set development environment

```bash
composer run-script start
```

2. Run test cases

```bash
composer run-script test
```

3. Run linter

```bash
composer run-script test
```

4. Run code beautifier

```bash
composer run-script phpcbf
```

## License

[Apache License, Version 2.0](https://github.com/wingify/vwo-php-sdk/blob/master/LICENSE)

Copyright 2019-2022 Wingify Software Pvt. Ltd.
