<?php

namespace OWolf\Laravel\Contracts;

use League\OAuth2\Client\Token\AccessToken;

interface OAuthHandler extends ProviderHandler
{
    /**
     * @param  array  $options
     * @return string
     */
    public function getAuthorizationUrl(array $options = []);

    /**
     * @param  string  $code
     * @return \League\OAuth2\Client\Token\AccessToken
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAccessTokenByCode($code);

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  string  $ownerId
     * @return bool
     */
    public function revokeToken(AccessToken $token, $ownerId);
}