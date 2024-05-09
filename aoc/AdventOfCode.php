<?php

namespace Mike\AdventOfCode;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Psr7\Response;
use Mike\AdventOfCode\Console\Application;
use Mike\AdventOfCode\Console\IO;

class AdventOfCode
{
    /**
     * The base application.
     */
    protected Application $app;

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
     * Create new instance of Advent of Code class.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->session = $app->config->get('aoc.session');

        $this->setupHttp();
    }

    /**
     * Fetch the information for given day.
     */
    public function getDay(int $year, int $day): AdventOfCodeDay
    {
        $this->validateDayAndYear($year, $day);

        $day = new AdventOfCodeDay($year, $day);

        $day->setClient($this);

        return $day;
    }

    /**
     * Validate given year and day.
     *
     * @throws \Mike\AdventOfCode\AdventOfCodeException
     */
    public function validateDayAndYear(int $year, int $day): static
    {
        [$currentYear, $currentMonth, $currentDay] = array_map('intval', explode('-', date('Y-m-d')));

        $maxYear = $currentMonth < 12 ? $currentYear-1 : $currentYear;
        $maxDays = $year == $currentYear && $currentDay < 26 ? $currentDay : 25;

        if ($year < 2015 || $year > $maxYear) {
            throw AdventOfCodeException::invalidYear($year, $maxYear);
        }

        if ($day < 1 || $day > $maxDays) {
            throw AdventOfCodeException::invalidDay($year, $day, $maxDays);
        }

        return $this;
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

            $headers = [
                'User-Agent' => $this->app->getUserAgent(),
            ];

            $this->http = new Client([
                'base_uri' => 'https://adventofcode.com',
                'headers' => $headers,
                'cookies' => $this->cookies = new CookieJar(true, [$sessionCookie]),
            ]);
        }

        return $this->http;
    }

    /**
     * Make a new request to the advent of code website.
     */
    public function request(string $method, string $uri = '', array $options = []): Response
    {
        return $this->http->request($method, $uri, $options);
    }

    /**
     * Show text when session has not been set in .env file.
     */
    public static function promptEmptySession(IO $io): void
    {
        $io->warn('Environment variable <white>AOC_SESSION</> is empty. Please fill it in in <white>.env</>.');
        $io->newLine();

        static::showSessionKeyInstructions($io);
    }

    /**
     * Show text when session has expired.
     */
    public static function promptExpiredSession(IO $io): void
    {
        $io->warn('The session provided in <white>AOC_SESSION</> has expired. Please get a new one.');
        $io->newLine();

        static::showSessionKeyInstructions($io);
    }

    /**
     * Print out instructions how to get the session key for advent of code.
     */
    public static function showSessionKeyInstructions(IO $io): void
    {
        $lines = [
            'To get the session follow these steps:',
            '1. Visit https://adventofcode.com',
            '2. After the site has loaded, press F12 or CTRL+SHIFT+I on your keyboard.',
            '3. After developer tools open, click on [Application] tab,',
            '   for chromium based browsers, or [Storage] tab for firefox.',
            '4. Expand Cookies from the left sidebar and click on https://adventofcode.com',
            '5. Copy the value from the cookie row with name session.',
            '6. Paste the value in .env file setting the AOC_SESSION variable.',
        ];

        array_map([$io, 'line'], $lines);
    }

    /**
     * Show invalid session key message to user.
     */
    public static function promptInvalidSessionKey(IO $io): void
    {
        $io->error('Provided session key in <white>AOC_SESSION</> environment variable is invalid.');
        $io->newLine();
        $io->line('Session key must be <fg=cyan>128 characters long</> and contain');
        $io->line('only <fg=cyan>lowercase letters <white>a</> through <white>f</></> and <fg=cyan>numbers <white>0</> through <white>9</></>.');
    }
}
