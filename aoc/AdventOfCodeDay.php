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
     */
    protected ?string $input = null;

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
     * Fetch the input for the current day and cache it.
     */
    protected function fetchInput(): string
    {
        try {
            $response = $this->request('GET', 'input');

            $input = trim($response->getBody()->getContents());

            $this->cacheInput($input);
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

        return $input;
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
     */
    public function cacheInput(string $input): bool
    {
        $path = $this->inputPath();

        if (! is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
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
            throw AdventOfCodeException::failedToFetchInfo($this->year, $this->day, $ex);
        }

        return $info;
    }

    /**
     * Try to fetch additional information for part 2 of the challenge.
     */
    public function part2IsUnlocked(): static
    {
        if (! $this->info('part2.unlocked')) {
            $this->fetchInfo(true);
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
        // the html tags allowed when getting the html from the page.
        $allowedTags = ['em'];

        $cached = $this->infoIsCached() ? $this->getCachedInfo() : [];

        $title = $cached['title'] ?? null;

        $part1 = array_merge([
            'question' => null,
            'answer' => null,
            'result' => null,
            'time' => null,
            'memory' => null,
        ], $cached['part1'] ?? []);

        $part1 = array_merge([
            'unlocked' => false,
            'question' => null,
            'answer' => null,
            'result' => null,
            'time' => null,
            'memory' => null,
        ], $cached['part2'] ?? []);

        $crawler = new Crawler($html);

        $parts = $crawler->filter('body > main > article.day-desc');
        $answers = $crawler->filter('body > main > article.day-desc + p');

        $p1 = $parts->first();

        $title = trim(trim($p1->filter('h2')->first()->text(), '-'));
        $title = substr($title, strpos($title, ':') + 2);

        $paragraphs = $p1->filter('p');
        $part1['question'] = strip_tags($paragraphs->last()->html(), $allowedTags);

        // if the last paragraph contains 'for example', use second-to-last paragraph as question.
        if (stripos($part1['question'], 'for example') === 0) {
            $part1['question'] = strip_tags($paragraphs->eq($paragraphs->count() - 2)->html(), $allowedTags);
        }
        $part1['question'] = preg_replace('/<(\w+)[^>]*>/', '<$1>', $part1['question']);

        if ($parts->count() == 2) {
            $part1['answer'] = $answers->first()->filter('code')->first()->text();

            $p2 = $parts->last();

            $part2['unlocked'] = true;

            $paragraphs = $p2->filter('p');
            $part2['question'] = strip_tags($paragraphs->last()->html(), $allowedTags);

            // if the last paragraph contains 'for example', use second-to-last paragraph as question.
            if (stripos($part2['question'], 'for example') === 0) {
                $part2['question'] = strip_tags($paragraphs->eq($paragraphs->count() - 2)->html(), $allowedTags);
            }

            $part2['question'] = preg_replace('/<(\w+)[^>]*>/', '<$1>', $part2['question']);

            if ($answers->count() == 2 && ($p2a = $answers->last()->filter('code'))->count()) {
                $part2['answer'] = $p2a->first()->text();
            }
        }

        return compact('title', 'part1', 'part2');
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
