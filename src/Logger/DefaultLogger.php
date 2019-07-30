<?php
namespace vwo\Logger;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
/**
 * 
 */

/***
 * Class DefaultLogger
 * By default monolog is implemented for logging
 * @package vwo\Logger
 */
class DefaultLogger implements LoggerInterface
{
    var $logger;

    /***
     * DefaultLogger constructor.
     *
     * used to initialize the monolog with line formatter
     *
     * @param int $minLevel
     * @param string $stream
     * @param string $settings
     */
    public function __construct($minLevel = Logger::INFO, $stream = "",$settings='')
    {
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n");
        if(!empty($stream)){
            $streamHandler = new StreamHandler($stream, $minLevel);
        }else{
            $streamHandler = new StreamHandler("php://stdout}", $minLevel);

        }
        $streamHandler->setFormatter($formatter);
        $this->logger = new Logger('VWO-SDK');
        $this->logger->pushHandler($streamHandler);
    }

    /***
     *
     * to add logs to monolog
     *
     * @param $msg
     * @param int $level
     * @return mixed|void
     */
	public function addLog($msg,$level=Logger::INFO){

        $x=$this->logger->addRecord($level,$msg);

	}
}


