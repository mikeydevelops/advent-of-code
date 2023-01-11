<?php

require_once __DIR__ . '/common.php';

function main()
{
    [$year, $day] = parseYearAndDayFromArgv();

    echo getInput($year, $day, false);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    main();
}
