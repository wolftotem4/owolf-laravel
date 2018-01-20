<?php

namespace OWolf\Laravel\Middleware;

use Closure;
use OWolf\Laravel\Exceptions\UserOAuthNotLoginException;
use OWolf\Laravel\UserOAuthManager;

class OAuthAuthenticate
{
    /**
     * @var \OWolf\Laravel\UserOAuthManager
     */
    protected $manager;

    /**
     * OAuthAuthenticate constructor.
     * @param \OWolf\Laravel\UserOAuthManager $manager
     */
    public function __construct(UserOAuthManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    protected function check($name)
    {
        return $this->manager->session($name)->check();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string    $provider
     * @return mixed
     *
     * @throws \OWolf\Laravel\Exceptions\UserOAuthNotLoginException
     */
    public function handle($request, Closure $next, $provider)
    {
        if (! $this->check($provider)) {
            throw new UserOAuthNotLoginException('Unauthenticated.', [$provider]);
        }

        return $next($request);
    }
}