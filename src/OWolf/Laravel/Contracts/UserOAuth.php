<?php

namespace OWolf\Laravel\Contracts;

interface UserOAuth
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user();

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function toAccessToken();
}