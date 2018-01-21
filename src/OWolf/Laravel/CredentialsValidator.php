<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Container\Container;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Contracts\OAuthHandler;

class CredentialsValidator
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * OAuthCredentialsValidator constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function validateOAuthProvider($name)
    {
        $manager = $this->container->make('owolf.provider');
        $handler = $manager->getHandler($name);
        return ($handler instanceof OAuthHandler);
    }

    /**
     * @param  string  $name
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return bool
     */
    public function validateOAuthBinding($name, AccessToken $token)
    {
        $manager = $this->container->make('owolf.provider');
        $handler = $manager->getHandler($name);
        if (! ($handler instanceof OAuthHandler)) {
            return false;
        }
        $repository = $this->container->make(UserOAuthRepository::class);
        $ownerId = $handler->getOwnerId($token);
        return $repository->isTokenBinded($name, $ownerId);
    }
}
