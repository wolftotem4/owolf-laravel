<?php

namespace OWolf\Laravel\Exceptions;

class UserOAuthNotLoginException extends OAuthException
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * UserOAuthNotLoginException constructor.
     * @param string  $message
     * @param array   $providers
     */
    public function __construct($message = "Unauthenticated OAuth.", array $providers = [])
    {
        parent::__construct($message);

        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function providers()
    {
        return $this->providers;
    }
}