# VWO PHP SDK

[![Latest Stable Version](https://img.shields.io/packagist/v/vwo/vwo-php-sdk.svg)](https://packagist.org/packages/vwo/vwo-php-sdk) [![Build Status](http://img.shields.io/travis/wingify/vwo-php-sdk/master.svg?style=flat)](http://travis-ci.org/wingify/vwo-php-sdk) [![Coverage Status](https://coveralls.io/repos/github/wingify/vwo-php-sdk/badge.svg?branch=master)](https://coveralls.io/github/wingify/vwo-php-sdk?branch=master)[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0)

VWO server side sdk helps in integrating you integrating the vwo features in backend.
Using the sdk you can fetch the campaigns , variations and goals which you have configured
in vwo app. Sdk will automatically calculate the variation that should be assigned to the user.
One can also send the goal track data to vwo app to check the conversions on the vwo dashborad.


## Requirements
* PHP 5.6 or later

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
require_once('userProfile.php'); // Optional :if you are using userProfile service feature
require_once('customLogger.php');// Optional :if you are using custom logging feature


use vwo\VWO;


$accountId = 123456;
$sdkKey = 'VWO_ACCOUNTK';
$campaignKey = 'CAMPAIGN_UNIQUE_TEST_KEY';
$userId = 'USER_IDENTIFIER';
$goalIdentifier = 'CAMPAIGN_GOAL_IDENTIFIER';


// to fetch the settings i.e campaigns, variations and goals
$settingsFile=VWO::getSettingsFile($accountId, $sdkKey);
$config=['settings'=>$settingsFile,
    'isDevelopmentMode'=>0,  // optional: 1 to enable the dev mode
    'logging'=>new CustomLogger(), // optional
    'userProfileService'=> new userProfile() // optional
];

$vwoClient = new VWO($config);

// to get the variation name along with add a visitor hit to vwo app stats
$varient=$vwoClient->activate($campaignKey, $userId);


// to get the variation name
$varient=$vwoClient->getVariation($campaignKey, $userId);


// add code here to use variation
//...


/**
*send the track api hit to the vwo app stats to increase conversions
* $revenue is optional send in case if there is any revenue
*/

$vwoClient->track($campaignKey, $userId, $goalIdentifier, $revenue);
```

**Code for UserProfile service**

```php
<?php
require_once('vendor/autoload.php');
use vwo\Utils\UserProfileInterface;
Class UserProfile implements UserProfileInterface{

    /**
     * @param $userId
     * @param $campaignKey
     * @return string
     */
    public function lookup($userId, $campaignKey){
        // xyz actions
       return[
            'userId'=>$userId,
            $campaignKey=>['variationName'=>'Control']
        ];

    }

    /**
     * @param $campaignInfo
     * @return bool
     */
    public function save($campaignInfo){
       // print_r($campaignInfo);
        return True;

    }

}
```

**Code for customLogger file**

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
     * @return string
     */
    public function addLog($message, $level){
        //do code for writing logs to your files/databases
        //throw new Exception('my test');
        //return $x;

    }

}
```

## Code Snippets

**Use the code below to fetch settings**

```php
// to fetch the settings i.e campaigns, variations and goals
$settingsFile=VWO::getSettings($accountId, $sdkKey);
```

**Use the code below to create a vwo instance**

```php
$config=['settingsFile'=> $settingsFile,
    'isDevelopmentMode'=> 0,  // optional: 1 to enable the dev mode
    'logger'=> new CustomLogger(), // optional
    'userProfileService'=> new userProfile() // optional
];

$vwoClient = new VWO($config);
```

**Use the code below to both activate campaign for a user and fetch variation name**

```php
// to get the variation name along with add a visitor hit to vwo app stats
$varient=$vwoClient->activate($campaignKey, $userId);
```

**Use the code below to get variation name**

```php
// to get the variation name along with add a visitor hit to vwo app stats
$varient=$vwoClient->getVariation($campaignKey, $userId);
```

**Use the code below to track**

```php
/**
*send the track api hit to the vwo app stats to increase conversions
* $revenue is optional send in case if there is any revenue
*/

$vwoClient->track($campaignKey, $userId, $goalIdentifier, $revenue);
```

## Documentation

Refer [Official VWO Documentation](https://developers.vwo.com/reference#server-side-introduction)


## Third-party Resources and Credits

Refer [third-party-attributions.txt](https://github.com/wingify/vwo-php-sdk/blob/master/third-party-attributions.txt)

## Development

1. Set dev environment

```bash
composer run-script start
```

2. Run test cases

```bash
composer run-script test
```

## License

[Apache License, Version 2.0](https://github.com/wingify/vwo-php-sdk/blob/master/LICENSE)

Copyright 2019 Wingify Software Pvt. Ltd.
