<?php

namespace OWolf\Laravel\Contracts;

interface ProviderHandler
{
    /**
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function provider();

    /**
     * @return string
     */
    public function name();

    /**
     * @param  string  $key
     * @return mixed
     */
    public function config($key = null);
}