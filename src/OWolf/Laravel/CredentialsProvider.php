<?php

namespace OWolf\Laravel;

use Illuminate\Support\ServiceProvider;

class CredentialsProvider extends ServiceProvider
{
    protected $defer = true;

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