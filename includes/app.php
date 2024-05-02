<?php

use Mike\AdventOfCode\Console\Application;

$app = new Application(dirname(__DIR__), '0.1a');

$app->singleton('client', require __DIR__ . '/client.php');

return $app;
