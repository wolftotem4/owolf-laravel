<?php

namespace Tests\Unit\Laravel;

use Carbon\Carbon;
use OWolf\Laravel\EncryptedAccessToken;
use PHPUnit\Framework\TestCase;

class EncryptedAccessTokenTest extends TestCase
{
    public function testEncryptedAccessToken()
    {
        $payload = 'mock_payload';
        $key     = 'mock_key';
        $expires = Carbon::now()->addHour();
        $encrypted = new EncryptedAccessToken($payload, $expires, $key);

        $html = '<input type="hidden" name="' . $key . '" value="' . $payload . '">';
        $this->assertEquals($key, $encrypted->getKey());
        $this->assertEquals($payload, (string) $encrypted);
        $this->assertEquals($html, $encrypted->toHtml());
        $this->assertTrue($expires->eq($encrypted->getExpires()));
    }
}
