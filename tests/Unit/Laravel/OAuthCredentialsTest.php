<?php

namespace Tests\Unit\Laravel;

use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\CredentialsValidator;
use OWolf\Laravel\OAuthCredentials;
use PHPUnit\Framework\TestCase;

class OAuthCredentialsTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\OWolf\Laravel\CredentialsValidator
     */
    protected $validatorMock;

    /**
     * @var \OWolf\Laravel\CredentialsValidator
     */
    protected function setUp()
    {
        parent::setUp();

        $this->validatorMock = \Mockery::mock(CredentialsValidator::class);
    }

    protected function tearDown()
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testMake()
    {
        $provider = 'mock_provider';
        $token = new AccessToken(['access_token' => 'mock_access_token']);

        $this->validatorMock
            ->shouldReceive('validateOAuthProvider')
            ->once()
            ->with($provider)
            ->andReturnTrue();

        $oauth = OAuthCredentials::make($provider, $token, $this->validatorMock);

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

        $this->validatorMock
            ->shouldReceive('validateOAuthProvider')
            ->once()
            ->andReturnFalse();

        OAuthCredentials::make($provider, $token, $this->validatorMock);
    }

    public function testIsBinded()
    {
        $provider = 'mock_provider';
        $token    = new AccessToken(['access_token' => 'mock_access_token']);

        $this->validatorMock
            ->shouldReceive('validateOAuthProvider')
            ->once()
            ->with($provider)
            ->andReturnTrue();

        $this->validatorMock
            ->shouldReceive('validateOAuthBinding')
            ->once()
            ->with($provider, $token)
            ->andReturnTrue();

        $oauth = new OAuthCredentials($provider, $token, $this->validatorMock);

        $this->assertTrue($oauth->isBinded());
    }
}
