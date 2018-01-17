<?php

namespace OWolf\Laravel\Exceptions;

class InvalidOAuthProviderException extends OAuthException
{
    public function __construct($message = "Invalid OAuth provider.")
    {
        parent::__construct($message);
    }
}