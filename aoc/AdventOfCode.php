<?php

namespace Mike\AdventOfCode;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class AdventOfCode
{
    /**
     * The HTTP client.
     */
    protected ?Client $http = null;

    /**
     * The cookies used to make requests with http client.
     */
    protected ?CookieJar $cookies = null;

    /**
     * The session id used to authenticate against adventofcode.com.
     */
    protected ?string $session = null;

    /**
     * The name of the application used to access adventofcode.com. Needed for User-Agent header.
     */
    protected ?string $appName = null;

    /**
     * The version of the application used to access adventofcode.com. Needed for User-Agent header.
     */
    protected ?string $appVersion = null;

    /**
     * Create new instance of Advent of Code class.
     */
    public function __construct(string $session = null, string $appName = null, string $appVersion = null)
    {
        $this->session = $session;
        $this->appName = $appName ?? 'Advent of Code by Mike';
        $this->appVersion = $appVersion ?? '1.0';

        $this->setupHttp();
    }

    /**
     * Setup the http client for requests.
     */
    protected function setupHttp(): Client
    {
        if (is_null($this->http)) {
            $sessionCookie = new SetCookie([
                'Name' => 'session',
                'Value' => $this->session,
                'Domain' => '.adventofcode.com',
                'Path' => '/',
                'Max-Age' => strtotime('+7 days'),
                'Expires' => strtotime('+30 days'),
                'Secure' => true,
                'Discard' => false,
                'HttpOnly' => false,
            ]);

            $headers = [];

            if ($this->appName && $this->appVersion) {
                $headers['User-Agent'] = "$this->appName/$this->appVersion";
            }

            $this->http = new Client([
                'base_uri' => 'https://adventofcode.com',
                'headers' => $headers,
                'cookies' => $this->cookies = new CookieJar(true, [$sessionCookie]),
            ]);
        }

        return $this->http;
    }
}
