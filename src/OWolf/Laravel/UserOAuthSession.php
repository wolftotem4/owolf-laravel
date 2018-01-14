<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Session\Session;

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
        $this->config    = $config;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function auth()
    {
        return $this->container->make(Authenticatable::class);
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
}