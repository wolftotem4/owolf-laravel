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
     * @param  array   $options
     * @return \League\OAuth2\Client\Token\AccessToken
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAccessTokenByCode($code, array $options = []);

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  string  $ownerId
     * @return bool
     */
    public function revokeToken(AccessToken $token, $ownerId);

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  bool  $cache
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function getResourceOwner(AccessToken $token, $cache = true);

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return mixed
     */
    public function getOwnerId(AccessToken $token);

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return string|null
     */
    public function getName(AccessToken $token);

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return string|null
     */
    public function getEmail(AccessToken $token);
}