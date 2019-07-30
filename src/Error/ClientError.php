<?php

namespace vwo\Error;

/**
 * Raised when a client error (400+) is returned from the API.
 */
class ClientError extends Error
{
    public function __toString()
    {
        return "Client Error ({$this->code}): " . $this->message;
    }
}
