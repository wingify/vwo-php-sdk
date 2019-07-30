<?php
namespace vwo\Utils;
/**
 * Interface UserProfileInterface
 *
 * user profile interface should be included and used to save and lookup
 * @package vwo\Utils
 */
interface UserProfileInterface {
    /**
     * @param $userId
     * @param $campaignName
     * @return mixed
     */
    public function lookup($userId,$campaignName);

    /**
     * @param $campaignInfo
     * @return mixed
     */
    public function save($campaignInfo);
}