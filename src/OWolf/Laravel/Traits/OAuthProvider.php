<?php

namespace OWolf\Laravel\Traits;

use League\OAuth2\Client\Token\AccessToken;

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
     * @return \League\OAuth2\Client\Token\AccessToken
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAccessTokenByCode($code)
    {
        return $this->provider()->getAccessToken('authorization_code', compact('code'));
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
}