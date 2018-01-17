<?php


namespace OWolf\Laravel\Traits;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Exceptions\InvalidOAuthProviderException;
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

            $authUrl = $handler->getAuthorizationUrl($this->authorizationParams($provider));
            $state = $handler->provider()->getState();

            $request->session()->put('oauth2state', $state);

            return Redirect::to($authUrl);
        } catch (InvalidOAuthProviderException $e) {
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
            $session    =  UserOAuth::session($provider);
            $handler    = $session->handler();

            if (! $this->validateState($request)) {
                App::abort(401, 'Invalid state parameter.');
            }

            $accessToken = $handler->getAccessTokenByCode($request->query('code'));

            if ($owner = $session->getByOwner($accessToken)) {
                $user = $session->auth()->getProvider()->retrieveById($owner->user_id);
                return $this->attemptLogin($provider, $user, $accessToken);
            } elseif ($session->auth()->check()) {
                $session->setAccessToken($accessToken);
                return Redirect::intended($this->redirectPath());
            } else {
                return $this->registerOAuth($provider, $accessToken);
            }
        } catch (InvalidOAuthProviderException $e) {
            App::Abort(500, 'Invalid OAuth provider.');
        } catch (IdentityProviderException $e) {
            App::abort(500, $e->getMessage());
        }
    }

    /**
     * @param  string  $provider
     * @return array
     */
    protected function authorizationParams($provider)
    {
        return [];
    }

    /**
     * @param  string  $provider
     * @param  mixed   $user
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function attemptLogin($provider, $user, AccessToken $accessToken)
    {
        $session    =  UserOAuth::session($provider);
        $auth       = $session->auth();

        if ($auth instanceof StatefulGuard) {
            $auth->login($user);
        } else {
            $auth->setUser($user);
        }

        $session->setAccessToken($accessToken);

        return Redirect::intended($this->redirectPath());
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
