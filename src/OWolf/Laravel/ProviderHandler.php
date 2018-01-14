<?php

namespace OWolf\Laravel;

use League\OAuth2\Client\Provider\AbstractProvider;

abstract class ProviderHandler
{
    /**
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $provider;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * ProviderHandler constructor.
     * @param \League\OAuth2\Client\Provider\AbstractProvider $provider
     * @param string  $name
     * @param array   $config
     */
    public function __construct(AbstractProvider $provider, $name, array $config)
    {
        $this->provider = $provider;
        $this->name     = $name;
        $this->config   = $config;
    }

    /**
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function provider()
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function config($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        } else {
            return array_get($this->config, $key);
        }
    }
}