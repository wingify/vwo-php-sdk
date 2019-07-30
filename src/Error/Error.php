<?php

namespace vwo\Error;

/**
 * Base class for API exceptions. Used if failOnError is TRUE.
 */
class Error extends \Exception
{
    public function __construct($message, $code)
    {
        if (is_array($message)) {
            $message = $message[0]->message;
        }

        parent::__construct($message, $code);
    }
}
