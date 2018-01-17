<?php

namespace OWolf\Laravel\Traits;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Facades\UserOAuth;
use OWolf\Laravel\UserOAuthSession;

trait OAuthAutoRegister
{
    /**
     * @param  string  $provider
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return mixed
     */
    protected function registerOAuth($provider, AccessToken $token)
    {
        $session    =  UserOAuth::session($provider);
        $email      = $session->handler()->getEmail($token);
        $user       = ($email) ? $this->getExistingUserByEmail($email) : null;
        if (! $user) {
            $name   = $session->handler()->getName($token);
            $user   = $this->createNewUser($name, $email);
        }

        return $this->attemptLogin($provider, $user, $token);
    }

    /**
     * @param  string  $email
     * @return mixed
     */
    protected function getExistingUserByEmail($email)
    {
        $model = App::make($this->userModel());
        return $model->where('email', $email)->first();
    }

    /**
     * @param  string  $name
     * @param  string  $email
     * @return mixed
     */
    protected function createNewUser($name, $email)
    {
        $userModel = $this->userModel();
        $user = new $userModel([
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make(random_bytes(32)),
        ]);
        $user->save();
        return $user;
    }

    /**
     * @return string
     */
    protected function userModel()
    {
        return 'App\\User';
    }
}