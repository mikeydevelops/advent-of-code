<?php

/** @var \Mike\AdventOfCode\Console\Application $app */

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

$sessionCookie = new SetCookie([
    'Name' => 'session',
    'Value' => $app->make('config')->get('aoc.session'),
    'Domain' => '.adventofcode.com',
    'Path' => '/',
    'Max-Age' => strtotime('+7 days'),
    'Expires' => strtotime('+30 days'),
    'Secure' => true,
    'Discard' => false,
    'HttpOnly' => false,
]);

$jar = new CookieJar(true, [$sessionCookie]);

$client = new Client([
    'base_uri' => 'https://adventofcode.com',
    'headers' => [
        'User-Agent' => $app->getName() . '/' . $app->getVersion(),
    ],
    'cookies' => $jar,
]);

return $client;
