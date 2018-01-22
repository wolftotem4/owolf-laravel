<?php

namespace OWolf\Laravel;

use Carbon\Carbon;
use Illuminate\Contracts\Encryption\Encrypter;
use OWolf\Laravel\Exceptions\AccessTokenEncryptionExpiredException;

class AccessTokenEncryption
{
    /**
     * @var int
     */
    protected $expiration = 3600;

    /**
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * EncryptedAccessToken constructor.
     * @param \Illuminate\Contracts\Encryption\Encrypter  $encrypter
     */
    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * @param  \OWolf\Laravel\OAuthCredentials  $oauth
     * @param  string  $key
     * @return \OWolf\Laravel\EncryptedAccessToken
     */
    public function encrypt(OAuthCredentials $oauth, $key = 'oauth')
    {
        $expires = Carbon::now()->addSeconds($this->expiration);

        $payload = [
            'oauth'     => $oauth,
            'expires'   => $expires->getTimestamp(),
        ];
        $payload = $this->encrypter->encrypt($payload);
        return new EncryptedAccessToken($payload, $expires, $key);
    }

    /**
     * @param  \OWolf\Laravel\EncryptedAccessToken|string  $payload
     * @return \OWolf\Laravel\OAuthCredentials
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     * @throws \OWolf\Laravel\Exceptions\AccessTokenEncryptionExpiredException
     */
    public function decrypt($payload)
    {
        $payload = $this->encrypter->decrypt($payload);
        $expires = Carbon::createFromTimestamp($payload['expires']);
        if ($expires->isPast()) {
            throw new AccessTokenEncryptionExpiredException('The access_token has been expired.');
        }
        return $payload['oauth'];
    }
}