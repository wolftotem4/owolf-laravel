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
}