<?php

namespace OWolf\Laravel;

use Illuminate\Support\ServiceProvider;
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
            __DIR__ . '/../../App/Http/Controllers/OAuth/LoginController.php'
                => app_path('Http/Controllers/OAuth/LoginController.php'),
            __DIR__ . '/../../config/owolf.php' => config_path('owolf.php'),
        ]);
    }

    public function register()
    {
        $this->registerCredentials();

        $this->registerProviderManager();

        $this->registerUserOAuth();

        $this->registerOAuthCache();

        $this->registerValidator();

        $this->app->singleton(AccessTokenEncryption::class);
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

    protected function registerValidator()
    {
        $this->app->singleton('owolf.validator', CredentialsValidator::class);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'owolf.credentials', 'user.oauth',
            'owolf.provider', 'owolf.validator',
            UserOAuthContract::class, UserOAuthRepository::class,
            UserOAuthManager::class, UserOAuthSession::class,
            AccessTokenEncryption::class,
        ];
    }
}