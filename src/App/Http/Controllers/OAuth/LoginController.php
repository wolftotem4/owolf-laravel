<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Facades\Hash;
use OWolf\Laravel\OAuthCredentials;
use OWolf\Laravel\Traits\Controller\OAuthAuthenticate;
use OWolf\Laravel\Traits\Controller\OAuthAutoRegister;

class LoginController extends Controller
{
    use OAuthAuthenticate, OAuthAutoRegister, RedirectsUsers;

    /**
     * @var string
     */
    protected $redirectTo = '/';

//    /**
//     * @param  \Illuminate\Http\Request  $request
//     * @param  string  $provider
//     * @param  \League\OAuth2\Client\Token\AccessToken  $token
//     * @return mixed
//     */
//    protected function registerOAuthForm(Request $request, $provider, AccessToken $token)
//    {
//        $credentials = OAuthCredentials::make($provider, $token);
//        $request->session()->put('oauth2register', $credentials);
//        return redirect()->route('register', ['oauth' => true]);
//    }
//
//    /**
//     * @param  string  $email
//     * @return mixed
//     */
//    protected function getExistingUserByEmail($email)
//    {
//        $model = app()->make($this->userModel());
//        return $model->where('email', $email)->first();
//    }
//
//    /**
//     * @param  string  $name
//     * @param  string  $email
//     * @return mixed
//     */
//    protected function createNewUser($name, $email)
//    {
//        $userModel = $this->userModel();
//        $user = new $userModel([
//            'name'      => $name,
//            'email'     => $email,
//            'password'  => Hash::make(random_bytes(32)),
//        ]);
//        $user->save();
//        return $user;
//    }
//
//    /**
//     * @return string
//     */
//    protected function userModel()
//    {
//        return 'App\\User';
//    }
}