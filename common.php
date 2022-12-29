<?php

/**
 * Get the input for given year and day or
 * auto detect year and day from debug_backtrace()
 *
 * @param  integer|null  $year
 * @param  integer|null  $day
 * @return string
 */
function getInput(int $year = null, int $day = null)
{
    if (is_null($year) or is_null($day)) {
        $src = debug_backtrace()[0];

        $dir = dirname($src['file']);

        $dayPart = intval(preg_replace('/\D*/', '', basename($dir)));

        $yearPart = intval(basename(dirname($dir)));

        if (is_null($year)) {
            if ($yearPart < 2015 || $yearPart > date('Y')) {
                return error('Unable to detect correct year, please specify correct year in [%s:%d]', $src['file'], $src['line']);
            }

            $year = $yearPart;
        }

        if (is_null($day)) {
            if ($dayPart < 1 || $dayPart > 25) {
                return error('Unable to detect correct day, please specify correct day in [%s:%d]', $src['file'], $src['line']);
            }

            $day = $dayPart;
        }
    }

    if ($input = getCachedInput($year, $day)) {
        return $input;
    }

    return fetchInput($year, $day);
}

/**
 * Fetch the input for the given year and day from Advent of Code servers.
 *
 * @param  integer  $year
 * @param  integer  $day
 * @return string
 */
function fetchInput(int $year, int $day) : string
{
    getSession();

    $ch = curl_init();

    $url = "https://adventofcode.com/$year/day/$day/input";

    $cookieJarFile = getSessionPath();

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mikey Develops Advent of Code Input Fetcher/1.0',
        CURLOPT_COOKIEFILE => $cookieJarFile,
        CURLOPT_COOKIEJAR => $cookieJarFile,
        CURLOPT_TIMEOUT => 10,
    ];

    curl_setopt_array($ch, $options);

    line("Fetching input for $year-12-$day from {$url}...");

    $input = curl_exec($ch);

    curl_close($ch);

    if ($input === false) {
        return error("Unable to fetch input $year-12-$day. Reason: %s", curl_error($ch));
    } else {
        line("Input fetched for $year-12-$day.");

        $input = trim($input);
    }

    saveCachedInput($year, $day, $input);

    return $input;
}

/**
 * Get the user session for Advent of Code server.
 *
 * @return string
 */
function getSession() : string
{
    if ($session = loadSession()) {
        return $session;
    }

    if (is_null($session)) {
        line('Your previous session has expired. Please login again and provide the new session key.');
        line('');
    }

    return askForSession();
}

/**
 * Prompt the user for session cookie value.
 *
 * @param  boolean  $describe
 * @return string
 */
function askForSession(bool $describe = true) : string
{
    if ($describe) {
        $lines = [
            'To get the session follow these steps:',
            '1. Visit https://adventofcode.com/',
            '2. Click on the padlock on the left of the url in your browser.',
            '3. Click on Cookies.',
            '4. On the popup that opened. In the site list, expand adventofcode.com',
            '5. Expand Cookies folder.',
            '6. Click on session cookie.',
            '7. Copy the value of Content field under the site list.',
            '8. Paste the value in the terminal.',
        ];

        print(implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL);
    }

    $session = trim(readline('Enter session cookie value: '));

    if (! preg_match('/^[a-f0-9]{128}$/', $session)) {
        $lines = [
            'Invalid session cookie value.',
            "Value must be \033[1;36m128 characters long\033[0m and contain",
            "only \033[1;36mlowercase letters a through f\033[0m",
            "and \033[1;36mnumbers 0 through 9\033[0m.",
        ];

        print(PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL);

        return askForSession(false);
    }

    saveSession($session);

    return $session;
}

/**
 * Load already cached session from disk.
 *
 * @param  boolean  $ignoreExpired  Return session event if it is expired.
 * @return string|false|null
 * @throws \Exception
 */
function loadSession(bool $ignoreExpired = false) : string|false|null
{
    $path = getSessionPath();

    if (! file_exists($path)) {
        return false;
    }

    $session = @file_get_contents($path);

    if ($session === false) {
        $error = error_get_last();

        return error('Unable to load session from [%s]. Reason: %s', $path, $error['message']);
    }

    if (! preg_match(getSessionCookiePattern(), $session, $matches)) {
        return false;
    }

    $expiry = intval($matches[1]);

    if (! $ignoreExpired && time() > $expiry) {
        return null;
    }

    $session = $matches[2];

    return trim($session);
}

/**
 * Save the session provided by the user.
 *
 * @param  string  $session
 * @return boolean
 * @throws \Exception
 */
function saveSession(string $session) : bool
{
    $path = getSessionPath();

    $previousSession = loadSession(true);

    $cookieLife = 35 * 24 * 60 * 60;

    $expiry = time() + $cookieLife;

    $cookie = ".adventofcode.com\tTRUE\t/\tTRUE\t$expiry\tsession\t$session";

    $cookies = (@file_get_contents($path)) ?: '';

    if ($previousSession) {
        preg_match(getSessionCookiePattern(), $cookies, $matches);

        $cookies = str_replace($matches[0], $cookie, $cookies);
    } else {
        $cookies .= $cookie . "\n";
    }

    $result = @file_put_contents($path, $cookies);

    if ($result === false) {
        $error = error_get_last();

        error('Unable to save session to [%s]. Reason: %s', $path, $error['message']);

        return false;
    }

    return true;
}

/**
 * Get the regex pattern that matches the session cookie for advent of code.
 *
 * @return string
 */
function getSessionCookiePattern()
{
    return '/^\.?adventofcode\.com.*?(\d{10})\s*session\s*(.*?)\s*$/m';
}

/**
 * Get the already downloaded input for the specified year and day.
 *
 * @param  integer  $year
 * @param  integer  $day
 * @return string|false  False when the cached file does not exist.
 * @throws \Exception
 */
function getCachedInput(int $year, int $day) : string|false
{
    $path = getCachedInputPath($year, $day);

    if (! file_exists($path)) {
        return false;
    }

    $input = @file_get_contents($path);

    if ($input === false) {
        $error = error_get_last();

        return error('Unable to load input from [%s]. Reason: %s', $path, $error['message']);
    }

    return trim($input);
}

/**
 * Save cached input for given day and year.
 *
 * @param  integer  $year
 * @param  integer  $day
 * @param  string  $input
 * @return boolean
 * @throws \Exception
 */
function saveCachedInput(int $year, int $day, string $input) : bool
{
    $path = getCachedInputPath($year, $day);

    $result = @file_put_contents($path, $input);

    if ($result === false) {
        $error = error_get_last();

        error('Unable to save input to [%s]. Reason: %s', $path, $error['message']);

        return false;
    }

    return true;
}

/**
 * Get the path to the cached input for the given year and day.
 *
 * @param  integer  $year
 * @param  integer  $day
 * @return string
 */
function getCachedInputPath(int $year, int $day) : string
{
    $path = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));

    $path[] = $year;
    $path[] = "day-$day";
    $path[] = 'input.txt';

    return implode(DIRECTORY_SEPARATOR, $path);
}

/**
 * Get the path to the cached session for Advent of Code.
 *
 * @return string
 */
function getSessionPath() : string
{
    $path = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));

    $path[] = 'session.aoc';

    return implode(DIRECTORY_SEPARATOR, $path);
}

/**
 * Throw an error using sprintf.
 *
 * @param  string  $message
 * @param  array  $formats,...
 * @return never
 * @throws \Exception
 */
function error(string $message, ...$formats)
{
    throw new Exception(sprintf($message, ...$formats));
}

/**
 * Print a line and END OF LINE character.
 *
 * @param  string  $message
 * @return int
 */
function line(string $message)
{
    return print($message . PHP_EOL);
}

/**
 * Explode safely, removing empty parts of the array.
 *
 * @param  string  $delimiter
 * @param  string  $input
 * @param  integer  $limit
 * @return array
 */
function explode_trim(string $delimiter, string $input, int $limit = PHP_INT_MAX) : array
{
    return array_values(array_filter(array_map('trim', explode($delimiter, $input, $limit))));
}

/**
 * Make 2d grid and fill it with given default value.
 *
 * @param  integer  $height
 * @param  integer  $width
 * @param  mixed  $fill
 * @return array
 */
function makeGrid(int $height, int $width, $fill = null) : array
{
    return array_fill(0, $height, array_fill(0, $width, $fill));
}
