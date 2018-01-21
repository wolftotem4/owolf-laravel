<?php

namespace OWolf\Laravel;

use Illuminate\Support\ServiceProvider;
use OWolf\Credentials\AccessTokenCredentials;
use OWolf\Laravel\Contracts\OAuthHandler;
use OWolf\Laravel\Contracts\UserOAuth as UserOAuthContract;

class CredentialsServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
            __DIR__ . '/../../App/UserOAuth.php' => app_path('UserOAuth.php'),
            __DIR__ . '/../../config/owolf.php' => config_path('owolf.php'),
        ]);

        OAuthCredentials::setProviderValidator(function ($name) {
            $manager = $this->app->make('owolf.provider');
            $handler = $manager->getHandler($name);
            return ($handler instanceof OAuthHandler);
        });

        OAuthCredentials::setBindingValidator(function ($name, $token) {
            $manager = $this->app->make('owolf.provider');
            $handler = $manager->getHandler($name);
            if (! ($handler instanceof OAuthHandler)) {
                return false;
            }
            $repository = $this->app->make(UserOAuthRepository::class);
            $ownerId = $handler->getOwnerId($token);
            return $repository->isTokenBinded($name, $ownerId);
        });
    }

    public function register()
    {
        $this->registerCredentials();

        $this->registerProviderManager();

        $this->registerUserOAuth();

        $this->registerOAuthCache();

        $this->app->make(AccessTokenEncryption::class);
    }

    protected function registerCredentials()
    {
        $this->app->singleton('owolf.credentials', function ($app) {
            return new CredentialsManager($app);
        });
    }

    protected function registerProviderManager()
    {
        $this->app->singleton('owolf.provider', function ($app) {
            return new ProviderManager($app);
        });
    }

    protected function registerUserOAuth()
    {
        $this->app->bind('user.oauth', function ($app) {
            $model = array_get($app['config']['owolf.user.oauth'], 'model', UserOAuth::class);
            return $app->make($model);
        });

        $this->app->alias('user.oauth', UserOAuthContract::class);

        $this->app->singleton(UserOAuthRepository::class);

        $this->app->singleton(UserOAuthManager::class, function ($app) {
            return new UserOAuthManager($app);
        });

        $this->app->bind(UserOAuthSession::class, function ($app, $args) {
            $auth = $app->make('auth.driver');
            return new UserOAuthSession($app, $auth, ...$args);
        });
    }

    protected function registerOAuthCache()
    {
        $this->app->singleton('owolf.oauth.cache', OAuthCache::class);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'owolf.credentials', 'user.oauth',
            'owolf.provider',
            UserOAuthContract::class, UserOAuthRepository::class,
            UserOAuthManager::class, UserOAuthSession::class,
        ];
    }
}