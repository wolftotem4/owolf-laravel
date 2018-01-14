<?php

namespace OWolf\Laravel\Contracts;

use League\OAuth2\Client\Token\AccessToken;

interface UserOAuth
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user();

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken);

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function toAccessToken();
}