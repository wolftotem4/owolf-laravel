<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Container\Container;
use League\OAuth2\Client\Token\AccessToken;

class UserOAuthSession
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \League\OAuth2\Client\Token\AccessToken
     */
    protected $accessToken;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $providerResolvers = [];

    /**
     * @var array
     */
    protected $provider = [];

    /**
     * UserOAuthSession constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     * @param string  $name
     * @param array   $config
     */
    public function __construct(Container $container, $name, array $config)
    {
        $this->container = $container;
        $this->name      = $name;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function auth()
    {
        return $this->container->make('auth');
    }

    /**
     * @return \OWolf\Laravel\UserOAuthRepository
     */
    public function repository()
    {
        return $this->container->make(UserOAuthRepository::class);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->auth()->getAuthIdentifier();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUser()
    {
        return $this->auth();
    }

    /**
     * @return \OWolf\Laravel\Contracts\UserOAuth|null
     */
    public function getUserOAuth()
    {
        return $this->repository()->getUserOAuth($this->getUserId(), $this->getName());
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getAccessToken()
    {
        $oauth = $this->getUserOAuth();
        return ($oauth) ? $oauth->toAccessToken() : null;
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->getUserOAuth()->setAccessToken($accessToken);
        return $this;
    }
}