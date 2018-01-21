<?php

namespace OWolf\Laravel;

use Closure;
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
     * @var \Closure
     */
    protected static $providerValidator;

    /**
     * @var \Closure
     */
    protected static $bindingValidator;

    /**
     * AccessTokenCredentials constructor.
     * @param string  $name
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
     */
    public function __construct($name, AccessToken $token)
    {
        $this->name = $name;
        $this->token = $token;

        if (! $this->validateOAuthProvider($name)) {
            throw new InvalidOAuthProviderException('Invalid OAuth provider: ' . htmlentities($name));
        }
    }

    /**
     * @param  string  $name
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return static
     */
    public static function make($name, AccessToken $token)
    {
        return new static($name, $token);
    }

    /**
     * @return \Closure
     */
    public static function getProviderValidator()
    {
        return static::$providerValidator;
    }

    /**
     * @param \Closure $closure
     */
    public static function setProviderValidator(Closure $closure)
    {
        static::$providerValidator = $closure;
    }

    /**
     * @return \Closure
     */
    public static function getBindingValidator()
    {
        return static::$bindingValidator;
    }

    /**
     * @param \Closure $closure
     */
    public static function setBindingValidator(Closure $closure)
    {
        static::$bindingValidator = $closure;
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
        $validator = static::getBindingValidator();
        if (! $validator) {
            throw new RuntimeException('OAuth binding validator is not set.');
        }

        return $validator($this->name, $this->token);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    protected function validateOAuthProvider($name)
    {
        $validator = static::getProviderValidator();
        if (! $validator) {
            throw new RuntimeException('Provider validator is not set.');
        }
        return $validator($name);
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