<?php

namespace OWolf\Laravel;

use Closure;
use Illuminate\Contracts\Container\Container;
use OWolf\Laravel\Contracts\OAuthHandler;
use OWolf\Laravel\Exceptions\InvalidOAuthProvider;

class ProviderManager
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $driverResolvers = [];

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * ProviderManager constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  string    $driver
     * @param  \Closure  $closure
     * @return $this
     */
    public function addDriver($driver, Closure $closure)
    {
        $this->driverResolvers[$driver] = $closure;
        return $this;
    }

    /**
     * @param  string  $name
     * @return \OWolf\Laravel\Contracts\ProviderHandler
     */
    protected function resolve($name)
    {
        $config = $this->container['config']["owolf.credentials.$name"];
        $driver = array_get($config, 'driver');

        if (!isset($this->driverResolvers[$driver])) {
            if ($driver) {
                throw new \RuntimeException('Invalid Credentials Driver: ' . htmlentities($driver));
            } else {
                throw new \RuntimeException('Credentials Driver is unspecified.');
            }
        }

        return $this->driverResolvers[$driver]($name, $config);
    }

    /**
     * @param  string  $name
     * @return \OWolf\Laravel\Contracts\ProviderHandler
     */
    public function getHandler($name)
    {
        if (! isset($this->providers[$name])) {
            $this->providers[$name] = $this->resolve($name);
        }

        return $this->providers[$name];
    }

    /**
     * @param  string  $name
     * @return \OWolf\Laravel\Contracts\OAuthHandler
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProvider
     */
    public function getOAuthHandler($name)
    {
        $handler = $this->getHandler($name);
        if (! ($handler instanceof OAuthHandler)) {
            throw new InvalidOAuthProvider('Invalid OAuth Provider: ' . $name);
        }
        return $handler;
    }

    /**
     * @param  string  $name
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function getProvider($name)
    {
        return $this->getHandler($name)->provider();
    }
}