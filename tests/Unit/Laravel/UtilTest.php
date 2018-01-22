<?php

namespace Tests\Unit\Laravel;

use Illuminate\Support\Facades\URL;
use Mockery;
use OWolf\Laravel\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testRedirectUri_url_1()
    {
        $redirectUri = 'url:oauth/callback';
        $url = 'http://mock.url/oauth/callback';
        $provider = 'mock_provider';

        URL::shouldReceive('to')
            ->once()
            ->with('oauth/callback')
            ->andReturn($url);

        $this->assertEquals($url, Util::redirectUri($redirectUri, $provider));
    }

    public function testRedirectUri_url_2()
    {
        $redirectUri = 'mock_uri';
        $provider = 'mock_provider';

        URL::shouldReceive('to')
            ->once()
            ->with($redirectUri)
            ->andReturn($redirectUri);

        $this->assertEquals($redirectUri, Util::redirectUri($redirectUri, $provider));
    }

    public function testRedirectUri_route()
    {
        $redirectUri = 'route:oauth.callback';
        $url = 'http://mock.url/oauth/callback/mock_provider';
        $provider = 'mock_provider';

        URL::shouldReceive('route')
            ->once()
            ->with('oauth.callback', [$provider])
            ->andReturn($url);

        $this->assertEquals($url, Util::redirectUri($redirectUri, $provider));
    }

    public function testRedirectUri_default()
    {
        $redirectUri = ''; // empty
        $url = 'http://mock.url/oauth/callback/mock_provider';
        $provider = 'mock_provider';

        URL::shouldReceive('route')
            ->once()
            ->with('oauth.callback', [$provider])
            ->andReturn($url);

        $this->assertEquals($url, Util::redirectUri($redirectUri, $provider));
    }
}
