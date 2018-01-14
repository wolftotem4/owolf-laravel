<?php

namespace OWolf\Laravel\Exceptions;

class InvalidOAuthProvider extends OAuthException
{
    public function __construct($message = "Invalid OAuth provider.")
    {
        parent::__construct($message);
    }
}