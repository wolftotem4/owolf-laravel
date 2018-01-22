<?php

namespace Tests\Unit\Laravel;

use Illuminate\Contracts\Encryption\Encrypter;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\AccessTokenEncryption;
use OWolf\Laravel\CredentialsValidator;
use OWolf\Laravel\OAuthCredentials;
use PHPUnit\Framework\TestCase;
use Mockery;

class AccessTokenEncryptionTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\OWolf\Laravel\CredentialsValidator
     */
    protected $validatorMock;

    /**
     * @var \Mockery\MockInterface|\Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypterMock;

    /**
     * @var \OWolf\Laravel\AccessTokenEncryption
     */
    protected $ate;

    protected function setUp()
    {
        parent::setUp();

        $this->validatorMock = Mockery::mock(CredentialsValidator::class);
        $this->encrypterMock = Mockery::mock(Encrypter::class);
        $this->ate           = new AccessTokenEncryption($this->encrypterMock);
    }

    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testEncryption()
    {
        $name = 'mock_name';
        $token = new AccessToken(['access_token' => 'mock_token']);
        $encrypted = 'mock_encrypted';

        $this->validatorMock
            ->shouldReceive('validateOAuthProvider')
            ->once()
            ->andReturnTrue();

        $oauth = OAuthCredentials::make($name, $token, $this->validatorMock);

        $this->encrypterMock
            ->shouldReceive('encrypt')
            ->once()
            ->andReturn($encrypted);

        $enc = $this->ate->encrypt($oauth);

        $this->encrypterMock
            ->shouldReceive('decrypt')
            ->once()
            ->with($encrypted)
            ->andReturn([
                'oauth' => $oauth,
                'expires' => $enc->getExpires()->getTimestamp(),
            ]);

        $dec = $this->ate->decrypt((string) $enc);

        $this->assertEquals($encrypted, strval($enc));
        $this->assertEquals($oauth, $dec);
    }
}
