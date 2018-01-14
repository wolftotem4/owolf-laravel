<?php

namespace OWolf\Laravel;

use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Contracts\UserOAuth as UserOAuthContract;

class UserOAuthRepository
{
    /**
     * @var \OWolf\Laravel\Contracts\UserOAuth|\Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * UserOAuthRepository constructor.
     * @param \OWolf\Laravel\Contracts\UserOAuth $model
     */
    public function __construct(UserOAuthContract $model)
    {
        $this->model = $model;
    }

    /**
     * @param  mixed   $userId
     * @param  string  $name
     * @return \OWolf\Laravel\Contracts\UserOAuth|null
     */
    public function getUserOAuth($userId, $name)
    {
        return $this->model->where('user_id', $userId)->where('name', $name)->first();
    }

    /**
     * @param  mixed   $userId
     * @param  string  $name
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return \OWolf\Laravel\Contracts\UserOAuth
     */
    public function setUserAccessToken($userId, $name, AccessToken $accessToken)
    {
        $oauth = $this->model->firstOrNew(['user_id' => $userId, 'name' => $name]);
        $oauth->setAccessToken($accessToken);
        return $oauth;
    }
}