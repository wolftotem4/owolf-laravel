<?php


namespace OWolf\Laravel\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Exceptions\InvalidOAuthProvider;
use OWolf\Laravel\Facades\CredentialsProvider;
use OWolf\Laravel\Facades\UserOAuth;

trait OAuthAuthenticate
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request, $provider)
    {
        try {
            $handler = CredentialsProvider::getOAuthHandler($provider);

            $authUrl = $handler->getAuthorizationUrl();
            $state = $handler->provider()->getState();

            $request->session()->push('oauth2state', $state);

            return Redirect::to($authUrl);
        } catch (InvalidOAuthProvider $e) {
            App::Abort(500, 'Invalid OAuth provider.');
        }
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request, $provider)
    {
        try {
            $session =  UserOAuth::session($provider);
            $handler  = $session->handler();
            $provider = $handler->provider();

            if (! $this->validateState($request)) {
                App::abort(401, 'Invalid state parameter.');
            }

            $accessToken = $handler->getAccessTokenByCode($request->query('code'));

            if (! $session->auth()->check()) {

                return $session->registerOAuth($provider, $accessToken);

            } else {

                return $this->attemptLogin($provider, $accessToken);
            }
        } catch (InvalidOAuthProvider $e) {
            App::Abort(500, 'Invalid OAuth provider.');
        } catch (IdentityProviderException $e) {
            App::abort(500, $e->getMessage());
        }
    }

//    /**
//     * @param  string $provider
//     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
//     */
//    public function link($provider)
//    {
//        $session = UserOAuth::session($provider);
//        $oauth = $session->getUserOAuth();
//
//        if (! $oauth) {
//
//            // 忽略空認證
//            // 忽略已綁定的 AccessToken
//            return Redirect::intended($this->redirectPath());
//
//        } elseif ($credentials instanceof OAuthUserCredentialsSession) {
//
//            return $this->registerOAuth($manager, $credentials);
//
//        } else {
//
//            return $this->attemptLogin($provider, $credentials);
//        }
//    }

//    /**
//     * @param  \OWolf\OAuth\Contracts\OAuthSessionManager $manager
//     * @return string
//     */
//    protected function getAuthorizationUrl(OAuthSessionManager $manager)
//    {
//        return $manager->getAuthorizationUrl($this->getAuthorizationParams($manager));
//    }
//
//    /**
//     * @param  \OWolf\OAuth\Contracts\OAuthSessionManager $manager
//     * @return array
//     */
//    protected function getAuthorizationParams(OAuthSessionManager $manager)
//    {
//        $repository = App::make(OAuthUserCredentialsRepository::class);
//        $params = [];
//
//        switch ($manager->driver()->config('driver')) {
//            case 'google':
//                if ($this->guard()->check()) {
//                    $credentials = $repository->provider($manager->name())->user($this->guard()->id())->first();
//                    if ($credentials) {
//                        $params['login_hint'] = $credentials->owner_id;
//                    } else {
//                        $params['approval_prompt'] = null;
//                        $params['prompt'] = 'consent';
//                    }
//                }
//                break;
//        }
//
//        return $params;
//    }

    /**
     * @param  string  $provider
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function attemptLogin($provider, AccessToken $accessToken)
    {
        UserOAuth::session($provider)->login($accessToken);

        return Redirect::intended($this->redirectPath());
    }

//    /**
//     * @param  \OWolf\OAuth\Contracts\OAuthSessionManager $manager
//     * @param  \OWolf\OAuth\OAuthCredentials\OAuthUserCredentialsSession $credentials
//     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
//     *
//     * @throws \OWolf\OAuth\Exceptions\OAuthOwnerHasTakenException
//     */
//    protected function registerOAuth(OAuthSessionManager $manager, OAuthUserCredentialsSession $credentials)
//    {
//        $owner = $manager->getOwnerInfo();
//
//        if (!$this->guard()->check()) {
//
//            if (method_exists($this, 'emailExists') && $this->emailExists($owner->getEmail())) {
//
//                return $this->showEmailExists();
//
//            } else {
//                $user = $this->registerNewUser($owner);
//
//                $this->guard()->login($user);
//            }
//        }
//
//        $credentials->toStore($this->guard()->id())->save();
//
//        return Redirect::intended($this->redirectPath());
//    }
//
//    /**
//     * @return \Illuminate\Http\Response
//     */
//    protected function showEmailExists()
//    {
//        return View::make('owolf::oauth.email_exists');
//    }
//
//    /**
//     * @param  string $provider
//     * @return \OWolf\OAuth\Contracts\OAuthSessionManager
//     *
//     * @throws \OWolf\OAuth\Exceptions\InvalidOAuthProviderException
//     */
//    protected function oauthSessionManager($provider)
//    {
//        return OAuth::make($provider);
//    }

//    /**
//     * Get the guard to be used during authentication.
//     *
//     * @return \Illuminate\Contracts\Auth\StatefulGuard
//     */
//    protected function guard()
//    {
//        return Auth::guard();
//    }

    /**
     * @param  string $provider
     * @return string
     */
    protected function oauthLinkRedirectTo($provider)
    {
        return URL::route('oauth.link', compact('provider'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function validateState(Request $request)
    {
        $knownState = $request->session()->get('oauth2state');
        $userState  = $request->query('state');

        return hash_equals($knownState, $userState);
    }
}
