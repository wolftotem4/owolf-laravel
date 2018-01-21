<?php

namespace OWolf\Laravel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AccessTokenEncryptionRequest extends Request
{
    /**
     * @param  string  $key
     * @return \OWolf\Laravel\OAuthCredentials
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function getOAuth($key)
    {
        $encryption = App::make(AccessTokenEncryption::class);
        return $encryption->decrypt($this->input($key));
    }
}