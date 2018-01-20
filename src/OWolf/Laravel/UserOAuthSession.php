<?php

namespace OWolf\Laravel;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Guard;
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
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @var array
     */
    protected $providerResolvers = [];

    /**
     * @var array
     */
    protected $provider = [];

    /**
     * @var \OWolf\Laravel\Contracts\OAuthHandler
     */
    protected $handler;

    /**
     * UserOAuthSession constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param string  $name
     * @param array   $config
     */
    public function __construct(Container $container, Guard $auth, $name, array $config)
    {
        $this->container = $container;
        $this->name      = $name;
        $this->auth      = $auth;

        $providerManager = $this->container->make('owolf.provider');
        $this->handler   = $providerManager->getOAuthHandler($this->getName());
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * @return \OWolf\Laravel\UserOAuthRepository
     */
    public function repository()
    {
        return $this->container->make(UserOAuthRepository::class);
    }

    /**
     * @return \OWolf\Laravel\Contracts\OAuthHandler
     */
    public function handler()
    {
        return $this->handler;
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
     * @return bool
     */
    public function check()
    {
        return $this->repository()->check($this->getUserId(), $this->getName());
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken|string  $ownerId
     * @return \OWolf\Laravel\Contracts\UserOAuth|null
     */
    public function getByOwner($ownerId)
    {
        if ($ownerId instanceof AccessToken) {
            $ownerId = $this->handler()->getOwnerId($ownerId);
        }

        // Avoid potential security issues.
        if (is_null($ownerId) || $ownerId === '' || $ownerId === false) {
            return null;
        }

        return $this->repository()->getByOwnerId($this->getName(), $ownerId);
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     * @throws \OWolf\Laravel\Exceptions\UserOAuthNotLoginException
     */
    public function getAccessToken()
    {
        $oauth = $this->getUserOAuth();
        if (! $oauth) {
            throw new UserOAuthNotLoginException('Unauthenticated', [$this->getName()]);
        }
        return $oauth->toAccessToken();
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken $accessToken
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        if (! $this->auth()->check()) {
            throw new AuthorizationException;
        }

        $ownerId = $this->handler()->getOwnerId($accessToken);
        $this->repository()->setUserAccessToken($this->getUserId(), $this->getName(), $ownerId, $accessToken);
        return $this;
    }
}