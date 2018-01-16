<?php

namespace OWolf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \OWolf\Laravel\OAuthCache setResourceDetail(\League\OAuth2\Client\Token\AccessToken $token, \League\OAuth2\Client\Provider\ResourceOwnerInterface $resourceDetails)
 * @method static \League\OAuth2\Client\Provider\ResourceOwnerInterface|null getResourceDetailCache(\League\OAuth2\Client\Token\AccessToken $token)
 * @method static \OWolf\Laravel\OAuthCache clearResourceDetailCache()
 * @method static \OWolf\Laravel\OAuthCache clearAllCache()
 */

class OAuthCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'owolf.oauth.cache';
    }
}