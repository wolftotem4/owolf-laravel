<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\App;
use JsonSerializable;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Exceptions\InvalidOAuthProviderException;
use Serializable;

class OAuthCredentials implements Serializable, Jsonable, JsonSerializable
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
     * @var string
     */
    protected static $defaultValidator = 'owolf.validator';

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
        $this->validator = $validator ?: $this->getDefaultValidator();

        if (! $this->validateOAuthProvider($name)) {
            throw new InvalidOAuthProviderException('Invalid OAuth provider: ' . htmlentities($name));
        }
    }

    /**
     * @param  string  $name
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  \OWolf\Laravel\CredentialsValidator      $validator
     * @return static
     *
     * @throws \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
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
     * @return \OWolf\Laravel\CredentialsValidator
     */
    protected function getDefaultValidator()
    {
        return App::make(static::$defaultValidator);
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

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->name         = $data['name'];
        $this->token        = $data['token'];
        $this->validator    = $this->getDefaultValidator();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }
}
