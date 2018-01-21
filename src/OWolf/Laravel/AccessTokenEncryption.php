<?php


namespace OWolf\Laravel;

use Illuminate\Contracts\Encryption\Encrypter;

class AccessTokenEncryption
{
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
        $payload = $this->encrypter->encrypt($oauth);
        return new EncryptedAccessToken($payload, $key);
    }

    /**
     * @param  \OWolf\Laravel\EncryptedAccessToken|string  $payload
     * @return \OWolf\Laravel\OAuthCredentials
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload)
    {
        return $this->encrypter->decrypt($payload);
    }
}