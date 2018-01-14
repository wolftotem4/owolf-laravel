<?php

namespace OWolf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use OWolf\Laravel\UserOAuthManager;

/**
 * @method static \OWolf\Laravel\UserOAuthSession session(string $name)
 */

class UserOAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserOAuthManager::class;
    }
}