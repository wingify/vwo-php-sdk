<?php
namespace vwo\Logger;
/***
 * Interface LoggerInterface
 * @package vwo\Logger
 */
interface LoggerInterface{
    /**
     * @param $msg
     * @param $level
     * @return mixed
     */
	public function addLog($msg,$level);
}