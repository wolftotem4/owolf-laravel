<?php

namespace OWolf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \OWolf\Laravel\ProviderManager addDriver(string $driver, \Closure $closure)
 * @method static \OWolf\Laravel\Contracts\ProviderHandler getHandler(string $name)
 * @method static \League\OAuth2\Client\Provider\AbstractProvider getProvider(string $name)
 */

class Provider extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'owolf.provider';
    }
}