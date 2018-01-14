<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Container\Container;

class UserOAuthManager
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * UserOAuthManager constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getAccessToken()
    {
        $this->container->make('user.oauth');
    }
}
