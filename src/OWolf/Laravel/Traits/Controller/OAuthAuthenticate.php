<?php


namespace OWolf\Laravel\Traits\Controller;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Exceptions\InvalidOAuthProviderException;
use OWolf\Laravel\Facades\CredentialsProvider;
use OWolf\Laravel\Facades\UserOAuth;
use RuntimeException;

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
            App::abort(500, 'Invalid OAuth provider.');
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
            $session    = UserOAuth::session($provider);
            $handler    = $session->handler();

            if (! $this->validateState($request)) {
                App::abort(401, 'Invalid state parameter.');
            }

            $accessToken = $handler->getAccessTokenByCode($request->query('code'));

            if ($owner = $session->getByOwner($accessToken)) {
                return $this->attemptLogin($provider, $owner->user_id, $accessToken);
            } elseif ($session->auth()->check()) {
                $session->setAccessToken($accessToken);
                return Redirect::intended($this->redirectPath());
            } else {
                return $this->registerOAuth($request, $provider, $accessToken);
            }
        } catch (InvalidOAuthProviderException $e) {
            App::abort(500, 'Invalid OAuth provider.');
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
     * @param  mixed   $userId
     * @param  \League\OAuth2\Client\Token\AccessToken  $accessToken
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function attemptLogin($provider, $userId, AccessToken $accessToken)
    {
        $session    = UserOAuth::session($provider);
        $auth       = $session->auth();

        if (! ($auth instanceof StatefulGuard)) {
            throw new RuntimeException('OAuth login is only for stateful session.');
        }

        $auth->loginUsingId($userId);
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
