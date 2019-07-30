<?php
namespace vwo\Logger;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
/**
 *
 */
class Loggers implements LoggerInterface
{

    function __construct($argument)
    {
        # code...
        // credentisla


    }
    function addLog($msg, $level){
        error_log($msg);
    }
}

