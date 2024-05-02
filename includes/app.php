<?php

use Mike\AdventOfCode\Console\Application;
use Mike\AdventOfCode\Console\OutputStyle;
use Mike\AdventOfCode\Support\Config;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$app = new Application(dirname(__DIR__), '0.1a');

$app->singleton('config', new Config(require __DIR__ . '/config.php'));

$formatter = new OutputFormatter(styles: [
    'info' => new OutputFormatterStyle('cyan'),
    'success' => new OutputFormatterStyle('green'),
    'warning' => new OutputFormatterStyle('yellow'),
    'emergency' => new OutputFormatterStyle('white', 'red'),
    'error' => new OutputFormatterStyle('red'),
    'danger' => new OutputFormatterStyle('red'),
    'comment' => new OutputFormatterStyle('gray'),
    'question' => new OutputFormatterStyle('magenta'),
    'white' => new OutputFormatterStyle('white'),
    'black' => new OutputFormatterStyle('black'),
]);

$input = $app->singleton('input', new ArgvInput);
$console = $app->singleton('console', new ConsoleOutput(formatter: $formatter));
$output = $app->singleton('output', new OutputStyle($input, $console));

$app->singleton('client', require __DIR__ . '/client.php');

return $app;
