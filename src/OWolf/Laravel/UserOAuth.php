<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Contracts\UserOAuth as UserOAuthContract;

class UserOAuth extends Model implements UserOAuthContract
{
    /**
     * @var string
     */
    protected $table = 'oauth_user_credentials';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'owner_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    /**
     * @var array
     */
    protected $dates = ['expires_at', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->fill([
            'access_token'  => $accessToken->getToken(),
            'owner_id'      => $accessToken->getResourceOwnerId(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires_at'    => $accessToken->getExpires(),
        ]);
        return $this;
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function toAccessToken()
    {
        return new AccessToken([
            'access_token'      => $this->access_token,
            'resource_owner_id' => $this->owner_id,
            'refresh_token'     => $this->refresh_token,
            'expires'           => ($this->expires_at) ? $this->expires_at->getTimestamp() : null,
        ]);
    }
}