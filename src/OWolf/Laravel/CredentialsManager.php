<?php

namespace OWolf\Laravel;

use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
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
     * @param  array   $config
     * @return \OWolf\Contracts\CredentialsInterface
     */
    protected function resolve(array $config)
    {
        $driver = array_get($config, 'driver');
        if (! isset($this->resolver[$driver])) {
            throw new InvalidArgumentException('Invalid Driver: ' . htmlentities($driver));
        }

        return $this->resolver[$driver]($this->container);
    }

    /**
     * @param  string  $name
     * @return \OWolf\Contracts\CredentialsInterface
     */
    public function get($name)
    {
        if (! isset($this->credentials[$name])) {
            $config = $this->container['config']->get("owolf.credentials.$name", []);
            $this->credentials[$name] = $this->resolve($config);
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
