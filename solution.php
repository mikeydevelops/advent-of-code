<?php

require_once __DIR__ . '/common.php';

function main()
{
    [$year, $day] = parseYearAndDayFromArgv();

    $path = sprintf('/%d/day-%02d/solution.php', $year, $day);
    $solution = __DIR__ . $path;

    if (! file_exists($solution)) {
        line("Error: Solution file [$path] does not exist.");

        return exit(1);
    }

    require_once $solution;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    main();
}
