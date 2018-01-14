<?php

namespace OWolf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \OWolf\Laravel\CredentialsManager addDriver(string $driver, \Closure $resolver)
 * @method static \OWolf\Contracts\CredentialsInterface get(string $name)
 * @method static \OWolf\Laravel\CredentialsManager set(string $name, \OWolf\Contracts\CredentialsInterface $credentials)
 * @method static bool has(string $name)
 */

class Credentials extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'owolf.credentials';
    }
}