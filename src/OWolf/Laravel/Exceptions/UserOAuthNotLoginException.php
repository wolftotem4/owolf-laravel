<?php

namespace OWolf\Laravel\Exceptions;

class UserOAuthNotLoginException extends OAuthException
{
    public function __construct($message = "Unauthenticated OAuth.")
    {
        parent::__construct($message);
    }
}