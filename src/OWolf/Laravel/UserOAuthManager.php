<?php

namespace OWolf\Laravel;

use Closure;
use Illuminate\Contracts\Container\Container;

class UserOAuthManager
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $session = array();

    /**
     * UserOAuthManager constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  string  $name
     * @return \OWolf\Laravel\UserOAuthSession
     */
    protected function resolveSession($name)
    {
        $config = $this->container['config']["owolf.credentials.$name"];
        return $this->container->make(UserOAuthSession::class, [$name, $config]);
    }

    /**
     * @param  string  $name
     * @return \OWolf\Laravel\UserOAuthSession
     */
    public function session($name)
    {
        if (! isset($this->session[$name])) {
            $this->session[$name] = $this->resolveSession($name);
        }
        return $this->session[$name];
    }
}
