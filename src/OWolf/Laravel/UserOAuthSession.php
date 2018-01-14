<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Container\Container;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Exceptions\UserOAuthNotLoginException;

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
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function auth()
    {
        return $this->container->make('auth.driver');
    }

    /**
     * @return \OWolf\Laravel\UserOAuthRepository
     */
    public function repository()
    {
        return $this->container->make(UserOAuthRepository::class);
    }

    /**
     * @return \OWolf\Laravel\Contracts\ProviderHandler
     */
    public function handler()
    {
        return $this->getHandler();
    }

    /**
     * @return \OWolf\Laravel\Contracts\ProviderHandler
     */
    public function getHandler()
    {
        $manager = $this->container->make('owolf.provider');
        return $manager->getHandler($this->getName());
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
        return $this->auth()->id();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUser()
    {
        return $this->auth()->user();
    }

    /**
     * @return \OWolf\Laravel\Contracts\UserOAuth|null
     */
    public function getUserOAuth()
    {
        return $this->repository()->getUserOAuth($this->getUserId(), $this->getName());
    }

    /**
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function provider()
    {
        return $this->getProvider();
    }

    /**
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function getProvider()
    {
        return $this->handler()->provider();
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     * @throws \OWolf\Laravel\Exceptions\UserOAuthNotLoginException
     */
    public function getAccessToken()
    {
        $oauth = $this->getUserOAuth();
        if (! $oauth) {
            throw new UserOAuthNotLoginException();
        }
        return $oauth->toAccessToken();
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->repository()->setUserAccessToken($this->getUserId(), $this->getName(), $accessToken);
        return $this;
    }
}