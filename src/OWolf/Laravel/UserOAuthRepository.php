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
     * @return bool
     */
    public function check($userId, $name)
    {
        return $this->model->where('user_id', $userId)->where('name', $name)->exists();
    }

    /**
     * @param  string  $name
     * @param  string  $ownerId
     * @return bool
     */
    public function isTokenBinded($name, $ownerId)
    {
        return $this->model->where('name', $name)->where('owner_id', $ownerId)->exists();
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
     * @param  string  $name
     * @param  string  $token
     * @return \OWolf\Laravel\Contracts\UserOAuth|null
     */
    public function getAccessToken($name, $token)
    {
        return $this->model->where('name', $name)->where('access_token', $token)->first();
    }

    /**
     * @param  string  $name
     * @param  mixed   $ownerId
     * @return \OWolf\Laravel\Contracts\UserOAuth|null
     */
    public function getByOwnerId($name, $ownerId)
    {
        return $this->model->where('name', $name)->where('owner_id', $ownerId)->first();
    }

    /**
     * @param  mixed   $userId
     * @param  string  $name
     * @param  string  $ownerId
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return \OWolf\Laravel\Contracts\UserOAuth
     */
    public function setUserAccessToken($userId, $name, $ownerId, AccessToken $accessToken)
    {
        $oauth = $this->model->firstOrNew(['user_id' => $userId, 'name' => $name, 'owner_id' => $ownerId]);
        $oauth->setAccessToken($accessToken);
        $oauth->save();
        return $oauth;
    }

    /**
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}