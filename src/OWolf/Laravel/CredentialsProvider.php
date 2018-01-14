<?php

namespace OWolf\Laravel;

use Illuminate\Support\ServiceProvider;

class CredentialsProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
            __DIR__ . '/../../App/UserOAuth.php' => app_path('UserOAuth.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('owolf.credentials', function ($app) {
            return new CredentialsManager($app);
        });
    }

    public function provides()
    {
        return [
            'owolf.credentials',
        ];
    }
}