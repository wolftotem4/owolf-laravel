<?php

namespace OWolf\Laravel\Traits;

use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Facades\OAuthCache;

trait OAuthProvider
{
    /**
     * @param  array  $options
     * @return string
     */
    public function getAuthorizationUrl(array $options = [])
    {
        if (array_has($this->config, 'scope')) {
            $scope      = $this->config['scope'];
            $options    = compact('scope') + $options;
        }
        return $this->provider()->getAuthorizationUrl($options);
    }

    /**
     * @param  string  $code
     * @param  array   $options
     * @return \League\OAuth2\Client\Token\AccessToken
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAccessTokenByCode($code, array $options = [])
    {
        return $this->provider()->getAccessToken('authorization_code', compact('code') + $options);
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  string  $ownerId
     * @return bool
     */
    public function revokeToken(AccessToken $token, $ownerId)
    {
        return true;
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  bool  $cache
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function getResourceOwner(AccessToken $token, $cache = true)
    {
        if ($cache) {
            $cache = OAuthCache::getResourceDetailCache($token) ?: $this->provider()->getResourceOwner($token);
            OAuthCache::setResourceDetail($token, $cache);
            return $cache;
        }

        return $this->provider()->getResourceOwner($token);
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return mixed
     */
    public function getOwnerId(AccessToken $token)
    {
        return $token->getResourceOwnerId() ?: $this->provider()->getResourceOwner($token)->getId();
    }
}