<?php

namespace Tests\Unit\Laravel;

use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\OAuthCredentials;
use PHPUnit\Framework\TestCase;

class OAuthCredentialsTest extends TestCase
{
    public function testMake()
    {
        $provider = 'mock_provider';
        $token = new AccessToken(['access_token' => 'mock_access_token']);

        OAuthCredentials::setProviderValidator(function ($name) use ($provider) {
            return ($name === $provider);
        });

        $oauth = OAuthCredentials::make($provider, $token);

        $this->assertTrue($oauth->is($provider));
        $this->assertEquals($provider, $oauth->name());
        $this->assertEquals($token, $oauth->getToken());
    }

    /**
     * @expectedException \OWolf\Laravel\Exceptions\InvalidOAuthProviderException
     */
    public function testInvalidOAuthProvider()
    {
        $provider = 'invalid_provider';
        $token = new AccessToken(['access_token' => 'mock_access_token']);

        OAuthCredentials::setProviderValidator(function ($name) {
            return ($name === 'valid_provider');
        });

        OAuthCredentials::make($provider, $token);
    }

    public function testIsBinded()
    {
        OAuthCredentials::setProviderValidator(function () {
            return true;
        });

        $provider = 'mock_provider';
        $token    = new AccessToken(['access_token' => 'mock_access_token']);

        OAuthCredentials::setBindingValidator(function ($name, $accessToken) use ($provider, $token) {
            $this->assertEquals($provider, $name);
            $this->assertEquals($token, $accessToken);
            return true;
        });

        $oauth = new OAuthCredentials($provider, $token);

        $this->assertTrue($oauth->isBinded());
    }
}
