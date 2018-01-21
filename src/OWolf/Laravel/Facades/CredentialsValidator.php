<?php

namespace OWolf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class CredentialsValidator extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'owolf.validator';
    }
}