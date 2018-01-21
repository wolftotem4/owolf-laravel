<?php

namespace OWolf\Laravel;

use Closure;
use Illuminate\Support\Facades\App;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Exceptions\InvalidOAuthProviderException;
use Symfony\Component\Console\Exception\RuntimeException;

class OAuthCredentials
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \League\OAuth2\Client\Token\AccessToken
     */
    protected $token;

    /**
     * @var \OWolf\Laravel\CredentialsValidator
     */
    protected $validator;

    /**
     * AccessTokenCredentials constructor.
     * @param string  $name
     * @param \League\OAuth2\Client\Token\AccessToken  $token
     * @param \OWolf\Laravel\CredentialsValidator      $validator
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
     */
    public function __construct($name, AccessToken $token, $validator = null)
    {
        $this->name      = $name;
        $this->token     = $token;
        $this->validator = $validator ?: App::make('owolf.validator');

        if (! $this->validateOAuthProvider($name)) {
            throw new InvalidOAuthProviderException('Invalid OAuth provider: ' . htmlentities($name));
        }
    }

    /**
     * @param  string  $name
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  \OWolf\Laravel\CredentialsValidator      $validator
     * @return static
     */
    public static function make($name, AccessToken $token, $validator = null)
    {
        return new static($name, $token, $validator);
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function is($name)
    {
        return $this->name == $name;
    }

    /**
     * @return bool
     */
    public function isBinded()
    {
        return $this->validator->validateOAuthBinding($this->name, $this->token);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    protected function validateOAuthProvider($name)
    {
        return $this->validator->validateOAuthProvider($name);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name'  => $this->name,
            'token' => $this->token,
        ];
    }
}