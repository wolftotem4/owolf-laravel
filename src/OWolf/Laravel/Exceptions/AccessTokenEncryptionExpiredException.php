<?php

namespace OWolf\Laravel\Exceptions;

use Exception;

class AccessTokenEncryptionExpiredException extends Exception
{
    public function __construct($message = 'The access_token has been expired.')
    {
        parent::__construct($message);
    }
}