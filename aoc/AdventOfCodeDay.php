<?php

namespace Mike\AdventOfCode;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class AdventOfCodeDay
{
    /**
     * The advent of code client.
     */
    protected AdventOfCode $client;

    /**
     * The year of the solution.
     */
    protected int $year;

    /**
     * The day of the solution.
     */
    protected int $day;

    /**
     * The day's input.
     *
     * @var string|resource|null
     */
    protected $input = null;

    /**
     * The day's information.
     */
    protected array $info = [];

    /**
     * Indicates that the info array is dirty and needs to be saved.
     */
    protected bool $infoChanged = false;

    /**
     * Create a new Advent of Code Day instance.
     */
    public function __construct(int $year, int $day)
    {
        $this->year = $year;
        $this->day = $day;
    }

    /**
     * Cleanup instance.
     */
    public function __destruct()
    {
        if ($this->infoChanged) {
            $this->saveInfo();
        }
    }

    /**
     * Get the day's input.
     */
    public function getInput(): string
    {
        if ($this->input) {
            return $this->input;
        }

        if ($this->inputIsCached()) {
            return $this->input = $this->getCachedInput();
        }

        return $this->input = $this->fetchInput();
    }

    /**
     * Stream the input, instead of loading it to memory.
     *
     * @param string $mode The mode parameter specifies the type of access you require to the stream.
     * It may be any of the following:
     *
     * 'r'  Open for reading only; place the file pointer at the beginning of the file.
     * 'r+' Open for reading and writing; place the file pointer at the beginning of the file.
     * 'w'  Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it.
     * 'w+' Open for reading and writing; otherwise it has the same behavior as 'w'.
     * 'a'  Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() has no effect, writes are always appended.
     * 'a+' Open for reading and writing; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() only affects the reading position, writes are always appended.
     * 'x'  Create and open for writing only; place the file pointer at the beginning of the file. If the file already exists, the fopen() call will fail by returning false and generating an error of level E_WARNING. If the file does not exist, attempt to create it. This is equivalent to specifying O_EXCL|O_CREAT flags for the underlying open(2) system call.
     * 'x+' Create and open for reading and writing; otherwise it has the same behavior as 'x'.
     * 'c'  Open the file for writing only. If the file does not exist, it is created. If it exists, it is neither truncated (as opposed to 'w'), nor the call to this function fails (as is the case with 'x'). The file pointer is positioned on the beginning of the file. This may be useful if it's desired to get an advisory lock (see flock()) before attempting to modify the file, as using 'w' could truncate the file before the lock was obtained (if truncation is desired, ftruncate() can be used after the lock is requested).
     * 'c+' Open the file for reading and writing; otherwise it has the same behavior as 'c'.
     * 'e'  Set close-on-exec flag on the opened file descriptor. Only available in PHP compiled on POSIX.1-2008 conform systems.
     * @param  resource|null  $context  A stream context resource.
     * @return resource
     */
    public function streamInput(string $mode = 'r', $context = null)
    {
        if ($this->inputIsCached()) {
            return $this->input = fopen($this->inputPath(), $mode, false, $context);
        }

        return $this->input = $this->fetchInput(true);
    }

    /**
     * Fetch the input for the current day and cache it.
     *
     * @param  boolean  $asStream  If true result will be returned as a resource.
     * @return string|resource
     */
    protected function fetchInput(bool $asStream = false)
    {
        try {
            $response = $this->request('GET', 'input');

            $this->cacheInput($input = $response->getBody()->detach());
        } catch (ClientException $ex) {
            if ($ex->getResponse()->getStatusCode() == 400) {
                $text = $ex->getResponse()->getBody()->getContents();

                if (stripos($text, 'please log in') !== false) {
                    throw AdventOfCodeException::promptExpiredSession($ex);
                }
            }

            throw AdventOfCodeException::failedToFetchInput($this->year, $this->day, $ex);
        } catch (Throwable $ex) {
            throw AdventOfCodeException::failedToFetchInput($this->year, $this->day, $ex);
        }

        rewind($input);

        return $asStream ? $input : stream_get_contents($input);
    }

    /**
     * Get the path of the day's input.
     */
    public function inputPath(): string
    {
        return base_path('storage', 'cache', 'inputs', strval($this->year), sprintf('day-%02d', $this->day));
    }

    /**
     * Check to see if the day's input has been downloaded and cached.
     */
    public function inputIsCached(): bool
    {
        return file_exists($this->inputPath());
    }

    /**
     * Load the day's input from storage.
     */
    public function getCachedInput(): string
    {
        return file_get_contents($this->inputPath());
    }

    /**
     * Cache given input.
     *
     * @param  string|resource  $input
     */
    public function cacheInput($input): bool
    {
        $path = $this->inputPath();

        if (! is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }

        if (is_resource($input)) {
            rewind($input);
        }

        return file_put_contents($path, $input) !== false;
    }

    /**
     * Make sure info about day is loaded.
     */
    protected function ensureInfoLoaded(): static
    {
        if (empty($this->info)) {
            $this->info = $this->infoIsCached()
                ? $this->getCachedInfo()
                : $this->fetchInfo();
        }

        return $this;
    }

    /**
     * Get the day's information.
     */
    public function info(string $key = null, mixed $default = null): mixed
    {
        $this->ensureInfoLoaded();

        if (is_null($key)) {
            return $this->info;
        }

        return array_get_dot($this->info, $key, $default);
    }

    /**
     * Set information about the day.
     */
    public function setInfo(string $key, mixed $value = null): static
    {
        $this->ensureInfoLoaded();

        $info = $this->info;

        array_set_dot($info, $key, $value);

        if ($info !== $this->info) {
            $this->info = $info;

            $this->infoChanged = true;
        }

        return $this;
    }

    /**
     * Fetch the information for the current day and cache it.
     */
    protected function fetchInfo(bool $force = false): array
    {
        try {
            $page = $this->getPage($force);

            $info = $this->parseInfo($page);

            $this->cacheInfo($info);
        } catch (Throwable $ex) {
            // throw AdventOfCodeException::failedToFetchInfo($this->year, $this->day, $ex);
            return static::defaultInfo();
        }

        return $info;
    }

    /**
     * Try to fetch additional information for part 2 of the challenge.
     */
    public function part2IsUnlocked(): static
    {
        if (! $this->info('part2.unlocked')) {
            $this->info = $this->fetchInfo(true);
        }

        return $this;
    }

    /**
     * Parse the information about the day.
     *
     * @param  string  $html
     * @return array
     */
    protected function parseInfo(string $html): array
    {
        $default = static::defaultInfo();
        $cached = $this->info ?: ($this->infoIsCached() ? $this->getCachedInfo() : $default);

        $title = $cached['title'] ?? $default['title'];

        $part1 = array_merge($default['part1'], $cached['part1'] ?? []);

        $part2 = array_merge($default['part2'], $cached['part2'] ?? []);

        $crawler = new Crawler($html);

        $parts = $crawler->filter('body > main > article.day-desc');
        $answers = $crawler->filter('body > main > article.day-desc + p');

        $p1 = $parts->first();

        $title = trim(trim($p1->filter('h2')->first()->text(), '-'));
        $title = substr($title, strpos($title, ':') + 2);

        $part1['question'] = $this->parseQuestion($p1->filter('p'));

        if ($parts->count() == 2) {
            $part1['answer'] = $answers->first()->filter('code')->first()->text();

            $p2 = $parts->last();

            $part2['unlocked'] = true;

            $part2['question'] = $this->parseQuestion($p2->filter('p'));

            if ($answers->count() == 2 && ($p2a = $answers->last()->filter('code'))->count()) {
                $part2['answer'] = $p2a->first()->text();
            }
        }

        return compact('title', 'part1', 'part2');
    }

    /** Get the default day info. */
    public static function defaultInfo(): array
    {
        return [
            'title' => null,
            'part1' => [
                'question' => null,
                'answer' => null,
                'result' => null,
                'time' => null,
                'memory' => null,
            ],
            'part2' => [
                'unlocked' => false,
                'question' => null,
                'answer' => null,
                'result' => null,
                'time' => null,
                'memory' => null,
            ],
        ];
    }

    /**
     * Parse the question from a list of paragraphs.
     */
    protected function parseQuestion(Crawler $paragraphs): string
    {
        $paragraphs = array_reverse(iterator_to_array($paragraphs));

        // the html tags allowed when getting the html from the page.
        $allowedTags = [
            'em', 'code',
        ];

        foreach ($paragraphs as $paragraph) {
            $question = strip_tags((new Crawler($paragraph))->html(), $allowedTags);

            // skip empty paragraphs or paragraphs that contain 'for example'
            if (empty($question) || stripos($question, 'for example') === 0) {
                continue;
            }

            $question = preg_replace('/<(\w+)[^>]*>/', '<$1>', $question);

            return $question;
        }

        return $question;
    }

    /**
     * Get the path of the day's information.
     */
    public function infoPath(): string
    {
        return base_path('storage', 'cache', 'days', strval($this->year), sprintf('day-%02d.json', $this->day));
    }

    /**
     * Check to see if the day's information has been downloaded and cached.
     */
    public function infoIsCached(): bool
    {
        return file_exists($this->infoPath());
    }

    /**
     * Load the day's information from storage.
     */
    public function getCachedInfo(): array
    {
        return json_decode(file_get_contents($this->infoPath()), true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * Cache given information.
     */
    public function cacheInfo(array $info): bool
    {
        $path = $this->infoPath();

        if (! is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($path, json_encode($info, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING)) !== false;
    }

    /**
     * Save the current information
     */
    public function saveInfo(): bool
    {
        if (empty($this->info)) {
            return true;
        }

        return $this->cacheInfo($this->info);
    }

    /**
     * Get the html of the current day.
     */
    protected function getPage(bool $force = false): string
    {
        if (! $force && $this->pageIsCached()) {
            return $this->getCachedPage();
        }

        try {
            // this gets the html for the current day
            // because the request automatically adds current year and day to the path.
            $response = $this->request('GET', '');
            $page = trim($response->getBody()->getContents());
        } catch (Throwable $ex) {
            throw AdventOfCodeException::failedToFetchPage($this->year, $this->day, $ex);
        }

        $this->verifyLoggedIn($page);

        $this->cachePage($page);

        return $page;
    }

    /**
     * Throw an exception if the user element is not present in html of page request.
     */
    protected function verifyLoggedIn(string $html): static
    {
        $crawler = new Crawler($html);

        if (! $crawler->filter('body > header .user')->count()) {
            throw AdventOfCodeException::promptExpiredSession();
        }

        return $this;
    }

    /**
     * Get the path of the day's html page.
     */
    public function pagePath(): string
    {
        return base_path('storage', 'cache', 'pages', strval($this->year), sprintf('day-%02d.html', $this->day));
    }

    /**
     * Check to see if the day's html page has been downloaded and cached.
     */
    public function pageIsCached(): bool
    {
        return file_exists($this->pagePath());
    }

    /**
     * Load the day's html page from storage.
     */
    public function getCachedPage(): string
    {
        return file_get_contents($this->pagePath());
    }

    /**
     * Cache given html page.
     */
    public function cachePage(string $html): bool
    {
        $path = $this->pagePath();

        if (! is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($path, $html) !== false;
    }

    /**
     * Make a request to the advent of code website,
     * with setting base uri to this day's year and day.
     */
    public function request(string $method, string $uri = '', array $options = []): Response
    {
        // adventofcode.com does not like trailing slashes.
        $uri = $uri && $uri[0] == '/' ? $uri : rtrim("/$this->year/day/$this->day/$uri", '/');

        return $this->client->request($method, $uri, $options);
    }

    /**
     * Get the class that will run the challenges.
     */
    public function getSolutionClass(): string
    {
        return "Mike\\AdventOfCode\\Solutions\\Year$this->year\\Day" . sprintf('%02d', $this->day);
    }

    /**
     * Get the solution year.
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Set the solution year.
     */
    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the solution day.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Set the solution day.
     */
    public function setDay(int $day): static
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Set the advent of code client.
     */
    public function setClient(AdventOfCode $client): static
    {
        $this->client = $client;

        return $this;
    }
}
