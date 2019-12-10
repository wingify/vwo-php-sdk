# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unrealsed, coming in v1.4.0]
### Added
Feature Rollout and Feature Test capabilities
- Introduced two new APIs i.e. `isFeatureEnabled` and `getFeatureVariableValue`
### Changed
- Existing APIs to handle new type of campaigns i.e. feature-rollout and feature-test
- Code refactored to support feature-rollout and feature-test capabilites

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
