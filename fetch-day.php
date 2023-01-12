<?php

require_once __DIR__ . '/common.php';

function main()
{
    [$year, $day] = parseYearAndDayFromArgv();

    echo MarkdownToAscii::convert(getMarkdown($year, $day));
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    main();
}
