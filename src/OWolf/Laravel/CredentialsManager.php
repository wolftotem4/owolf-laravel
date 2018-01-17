<?php

namespace OWolf\Laravel;

use Closure;
use Illuminate\Contracts\Container\Container;
use OWolf\Contracts\CredentialsInterface;

class CredentialsManager
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $resolver = [];

    /**
     * @var array
     */
    protected $credentials = [];

    /**
     * CredentialsManager constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  string    $driver
     * @param  \Closure  $resolver
     * @return $this
     */
    public function addDriver($driver, Closure $resolver)
    {
        $this->resolver[$driver] = $resolver;
        return $this;
    }

    /**
     * @param  string  $name
     * @param  array   $config
     * @return \OWolf\Contracts\CredentialsInterface
     */
    protected function resolve($name, array $config)
    {
        $driver = array_get($config, 'driver');
        if (! isset($this->resolver[$driver])) {
            if ($driver) {
                throw new \RuntimeException('Invalid Driver: ' . htmlentities($driver));
            } else {
                throw new \RuntimeException('No driver specified');
            }
        }

        return $this->resolver[$driver]($name, $config);
    }

    /**
     * @param  string  $name
     * @return \OWolf\Contracts\CredentialsInterface
     */
    public function get($name)
    {
        if (! isset($this->credentials[$name])) {
            if (! array_has($this->container['config']["owolf.credentials"], $name)) {
                throw new \RuntimeException('Invalid credentials: ' . htmlentities($name));
            }

            $config = array_get($this->container['config']['owolf.credentials'], $name);
            $this->credentials[$name] = $this->resolve($name, $config);
        }
        return $this->credentials[$name];
    }

    /**
     * @param  string  $name
     * @param  \OWolf\Contracts\CredentialsInterface  $credentials
     * @return $this
     */
    public function set($name, CredentialsInterface $credentials)
    {
        $this->credentials[$name] = $credentials;
        return $this;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->credentials[$name]) || $this->container['config']->has("owolf.credentials.$name");
    }
}
