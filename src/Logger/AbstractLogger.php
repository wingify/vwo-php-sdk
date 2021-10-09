<?php


namespace vwo\Logger;


use Monolog\Logger as Logger;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractLogger
 * An abstract logger class that conforms to the PSR-3 logger spec
 *
 * @package vwo\Logger
 */
abstract class AbstractLogger implements LoggerInterface
{

    /**
     * @inheritdoc
     */
    public function emergency($message, array $context = array())
    {
        $this->log(Logger::EMERGENCY, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function alert($message, array $context = array())
    {
        $this->log(Logger::ALERT, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function critical($message, array $context = array())
    {
        $this->log(Logger::CRITICAL, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function error($message, array $context = array())
    {
        $this->log(Logger::ERROR, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function warning($message, array $context = array())
    {
        $this->log(Logger::WARNING, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function notice($message, array $context = array())
    {
        $this->log(Logger::NOTICE, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function info($message, array $context = array())
    {
        $this->log(Logger::INFO, $message, $context);
    }

    /**
     * @inheritdoc
     */
    public function debug($message, array $context = array())
    {
        $this->log(Logger::DEBUG, $message, $context);
    }

}
