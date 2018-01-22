<?php

namespace OWolf\Laravel;

use Illuminate\Support\Facades\URL;

class Util
{
    /**
     * @param  string  $uri
     * @param  string  $provider
     * @return mixed
     */
    public static function redirectUri($uri, $provider)
    {
        if (preg_match('/^url:((?>.*))/i', $uri, $match)) {
            return URL::to($match[1]);
        }

        if (preg_match('/^route:((?>.*))/i', $uri, $match)) {
            return URL::route($match[1], [$provider]);
        }

        return ($uri) ? URL::to($uri) : URL::route('oauth.callback', [$provider]);
    }
}