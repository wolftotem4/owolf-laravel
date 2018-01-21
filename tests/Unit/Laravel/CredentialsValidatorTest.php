<?php

namespace Tests\Unit\Laravel;

use Illuminate\Container\Container;
use League\OAuth2\Client\Token\AccessToken;
use Mockery;
use OWolf\Laravel\Contracts\OAuthHandler;
use OWolf\Laravel\CredentialsValidator;
use OWolf\Laravel\ProviderManager;
use OWolf\Laravel\UserOAuthRepository;
use PHPUnit\Framework\TestCase;

class CredentialsValidatorTest extends TestCase
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \Mockery\MockInterface|\OWolf\Laravel\ProviderManager
     */
    protected $providerMock;

    /**
     * @var \Mockery\MockInterface|\OWolf\Laravel\Contracts\OAuthHandler
     */
    protected $handlerMock;

    /**
     * @var \Mockery\MockInterface|\OWolf\Laravel\UserOAuthRepository
     */
    protected $repositoryMock;

    /**
     * @var \OWolf\Laravel\CredentialsValidator
     */
    protected $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->container        = new Container();
        $this->providerMock     = Mockery::mock(ProviderManager::class);
        $this->handlerMock      = Mockery::mock(OAuthHandler::class);
        $this->repositoryMock   = Mockery::mock(UserOAuthRepository::class);
        $this->validator        = new CredentialsValidator($this->container);

        $this->container->instance('owolf.provider', $this->providerMock);
        $this->container->instance(UserOAuthRepository::class, $this->repositoryMock);
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testValidateOAuthProvider()
    {
        $provider = 'mock_provider';

        $this->providerMock
            ->shouldReceive('getHandler')
            ->once()
            ->with($provider)
            ->andReturn($this->handlerMock);

        $returnValue = $this->validator->validateOAuthProvider($provider);
        $this->assertTrue($returnValue);
    }

    public function testValidateOAuthBinding()
    {
        $provider = 'mock_provider';
        $ownerId  = 'mock_owner_id';
        $token    = new AccessToken(['access_token' => 'mock_access_token']);

        $this->providerMock
            ->shouldReceive('getHandler')
            ->once()
            ->with($provider)
            ->andReturn($this->handlerMock);

        $this->handlerMock
            ->shouldReceive('getOwnerId')
            ->once()
            ->with($token)
            ->andReturn($ownerId);

        $this->repositoryMock
            ->shouldReceive('isTokenBinded')
            ->once()
            ->with($provider, $ownerId)
            ->andReturnTrue();

        $returnValue = $this->validator->validateOAuthBinding($provider, $token);

        $this->assertTrue($returnValue);
    }
}
