<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Parser\Block\HtmlBlockParser;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use Symfony\Component\DomCrawler\Crawler;

$_http = null;

/**
 * Get the http client for Advent of Code.
 *
 * @return \GuzzleHttp\Client
 */
function getClient() : Client
{
    global $_http;

    if (is_null($_http)) {
        $_http = new Client([
            'base_uri' => 'https://adventofcode.com',
            'headers' => [
                'User-Agent' => 'Mikey Develops/1.0',
            ],
            'cookies' => getAdventOfCodeCookieJar(),
        ]);
    }

    return $_http;
}

/**
 * Get the input for given year and day or
 * auto detect year and day from debug_backtrace()
 *
 * @param  integer|null  $year
 * @param  integer|null  $day
 * @param  boolean  $verbose  Whether to print status of download.
 * @return string
 */
function getInput(int $year = null, int $day = null, bool $verbose = true) : string
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

    return fetchInput($year, $day, $verbose);
}

/**
 * Fetch the input for the given year and day from Advent of Code servers.
 *
 * @param  integer  $year
 * @param  integer  $day
 * @param  boolean  $verbose  Whether to print status of download.
 * @return string
 */
function fetchInput(int $year, int $day, bool $verbose = true) : string
{
    $client = getClient();

    $uri = "/$year/day/$day/input";

    if ($verbose) {
        line("Fetching input for $year-12-$day from {$uri}...");
    }

    try {
        $response = $client->get($uri);

        $input = $response->getBody()->getContents();
    } catch (Throwable $ex) {
        throw new Exception(sprintf("Unable to fetch input $year-12-$day. Reason: %s", $ex->getMessage()), $ex->getCode(), $ex);
    }

    if ($verbose) {
        line("Input fetched for $year-12-$day.");
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
 * Get the cookie jar for Advent of Code.
 *
 * @return \GuzzleHttp\Cookie\CookieJar
 * @throws \Exception
 */
function getAdventOfCodeCookieJar() : CookieJar
{
    getSession();

    return new CookieJar(true, parseNetscapeCookies(file_get_contents(getSessionPath())));
}

/**
 * Extract any cookies found from Netscape formatted cookie string.
 *
 * @param string $string Netscape formatted cookies string.
 *
 * @return array The array of cookies as extracted from the string.
 */
function parseNetscapeCookies(string $string) : array
{
    $cookies = [];

    $lines = preg_split("/\r?\n/", $string);

    // iterate over lines
    foreach ($lines as $line) {

        // we only care for valid cookie def lines
        if (isset($line[0]) && substr_count($line, "\t") == 6) {

            // get tokens in an array
            $tokens = explode("\t", $line);

            // trim the tokens
            $tokens = array_map('trim', $tokens);

            $cookie = [];

            // Extract the data
            $cookie['Domain'] = $tokens[0];
            // $cookie['flag'] = $tokens[1];
            $cookie['Path'] = $tokens[2];
            $cookie['Secure'] = $tokens[3];

            // Convert date to a readable format
            $cookie['Expires'] = intval($tokens[4]);

            $cookie['Name'] = $tokens[5];
            $cookie['Value'] = $tokens[6];

            // Record the cookie.
            $cookies[] = $cookie;
        }
    }

    return $cookies;
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
    $path = storage_path(sprintf('inputs/%d/day-%02d', $year, $day));

    if (! is_dir($dir = dirname($path))) {
        mkdir($dir, 0777, true);
    }

    return $path;
}

/**
 * Get the path to the cached session for Advent of Code.
 *
 * @return string
 */
function getSessionPath() : string
{
    $path = storage_path('session.aoc');

    if (! is_dir($dir = dirname($path))) {
        mkdir($dir, 0777, true);
    }

    return $path;
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

/**
 * Wrap a value in array.
 * If value is already array return the same array.
 * If value is null return empty array.
 *
 * @param  mixed  $arr
 * @return array
 */
function array_wrap($arr) : array
{
    if (is_null($arr)) {
        return [];
    }

    if (is_array($arr)) {
        return $arr;
    }

    return [$arr];
}

/**
 * Search one or multiple needles in a string.
 *
 * @param  string  $haystack
 * @param  string|string[]  $needles
 * @param  boolean $ignoreCase
 * @return boolean
 */
function string_contains(string $haystack, $needles, bool $ignoreCase = false) : bool
{
    $needles = array_wrap($needles);

    $func = $ignoreCase ? 'stripos' : 'strpos';

    foreach ($needles as $needle) {
        if (call_user_func($func, $haystack, $needle) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Calculate permutations of array.
 *
 * @param  array  $array
 * @return array
 */
function combinations($array, $size = 3, $combinations = [])
{
    if (empty($combinations)) {
        $combinations = $array;
    }

    if ($size == 1) {
        return $combinations;
    }

    $newCombination = [];

    foreach ($array as $key => $val) {
        foreach ($combinations as $item) {
            $item = array_wrap($item);

            if(in_array($val, $item)) {
                continue;
            }

            $newCombination[] = array_merge(array_wrap($val), $item);
        }
    }

    return combinations($array, $size - 1, $newCombination);
}

/**
 * Create chunks representing a "sliding window" view of the items in the collection.
 *
 * @param  array  $array
 * @param  int  $size
 * @param  int  $step
 * @return array
 */
function array_sliding(array $array, $size = 2, $step = 1)
{
    $chunks = floor((count($array) - $size) / $step) + 1;

    $windows = [];

    foreach (range(1, $chunks) as $number) {
        $windows[] = array_slice($array, ($number - 1) * $step, $size);
    }

    return $windows;
}


/**
 * Find repeating items in an array.
 *
 * @param  array  $items
 * @return array
 */
function findRepeatingItems(array $items)
{
    $overlapping = [];

    foreach ($items as $idx => $item) {
        $next = $items[$idx + 1] ?? false;

        if ($next === false) {
            break;
        }

        if ($item !== $next) {
            continue;
        }

        if (in_array($item, $overlapping)) {
            continue;
        }

        $overlapping[] = $item;
    }

    return $overlapping;
}

/**
 * Check to see if given array is associative or not.
 *
 * @param  array  $array
 * @return boolean
 */
function array_is_assoc(array $array) : bool
{
    $keys = array_keys($array);

    return array_keys($keys) !== $keys;
}

/**
 * Filter array recursively.
 *
 * @param  array  $array
 * @param  null|callable  $callback
 * @param  integer  $mode
 * @return array
 */
function array_filter_recursive(array $array, ?callable $callback, int $mode = 0) : array
{
    $array = array_filter($array, function ($item) use ($callback, $mode) {
        if (is_array($item)) {
            return array_filter_recursive($item, $callback, $mode);
        }

        return $callback($item);
    });

    return array_filter($array, $callback, $mode);
}

/**
 * Get the path to the storage folder.
 *
 * @param  string  $append...
 * @return string
 */
function storage_path(string $append = null) : string
{
    $ds = DIRECTORY_SEPARATOR;
    $dir = __DIR__;
    $path = "$dir{$ds}storage{$ds}" . implode($ds, array_filter(func_get_args(), 'is_string'));

    return preg_replace('/[\\\\\/]+/', $ds, $path);
}

/**
 * Get the last valid year for Advent of Code.
 *
 * @return integer
 */
function getEndYear() : int
{
    $year = date('Y');

    if (date('m') < 12) {
        $year --;
    }

    return $year;
}

/**
 * Helper function to print the usage when year and date are needed.
 *
 * @return void
 */
function printYearDayUsage(string $type = null)
{
    global $argv;

    if (is_null($type) || $type === 'args') {
        line("Usage: php $argv[0] <year> <day>");
    }

    if (is_null($type) || $type === 'single') {
        line("Usage: php $argv[0] <year-day> Ex: 2015-09, 2017-1");
    }

    $endYear = getEndYear();

    line("Usage: Where year can be from 2015 to $endYear.");
    line("Usage: Where day can be from 1 to 25.");
}

/**
 * Parse the year and day from argv.
 *
 * @return int[]
 */
function parseYearAndDayFromArgv() : array
{
    global $argv;

    $year = $argv[1] ?? null;

    if (is_null($year)) {
        printYearDayUsage();

        return exit(1);
    }

    $day = $argv[2] ?? null;

    $format = strpos($year, '-') === false ? 'args' : 'single';

    if ($format == 'single') {
        [$year, $day] = explode('-', $year);

        if (! is_numeric($day)) {
            $day = null;
        }
    }

    if (is_null($day)) {
        printYearDayUsage($format);

        return exit(1);
    }

    $endYear = getEndYear();

    if ($year < 2015 || $year > $endYear || $day < 1 || $day > 25) {
        printYearDayUsage($format);
        line('Error: Invalid value for year or day.');

        return exit(2);
    }

    return [intval($year), intval($day)];
}

function getPage(int $year, int $day) : string
{
    return '';
}

function getMarkdown(int $year, int $day) : string
{
    $page = new Crawler(getPage($year, $day));

    $env = new Environment([]);
    $env->addExtension(new CommonMarkCoreExtension);
    $env->addExtension(new GithubFlavoredMarkdownExtension);

    $parser = new HtmlBlockParser(1);

    return '';
}
