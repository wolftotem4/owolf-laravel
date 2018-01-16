<?php

namespace OWolf\Laravel\Traits;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\UserOAuthSession;

trait OAuthAutoRegister
{
    /**
     * @param \OWolf\Laravel\UserOAuthSession          $session
     * @param \League\OAuth2\Client\Token\AccessToken  $token
     */
    protected function registerOAuth(UserOAuthSession $session, AccessToken $token)
    {
        $email = $session->handler()->getEmail($token);
        $user  = ($email) ? $this->getExistingUserByEmail($email) : null;
        $auth  = $session->auth();
        if (! $user) {
            $name = $session->handler()->getName($token);
            $user = $this->createNewUser($name, $email);
        }

        if ($auth instanceof StatefulGuard) {
            $auth->login($user);
        } else {
            $auth->setUser($user);
        }

        $session->setAccessToken($token);

        return Redirect::intended($this->redirectPath());
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
        return Config::get('owolf.user.oauth.model', 'App\\User');
    }
}