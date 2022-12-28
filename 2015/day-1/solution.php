<?php

require_once __DIR__ . '/../../common.php';

$input = getInput();

$floor = 0;
$basementPosition = 0;

foreach (str_split($input) as $idx => $instruction) {
    if ($instruction == '(') {
        $floor ++;
    }

    if ($instruction == ')') {
        $floor --;
    }

    if ($basementPosition === 0 && $floor == -1) {
        $basementPosition = $idx + 1;
    }
}

print("1. Santa will need to go to floor: $floor");
print("2. The position of the character that causes Santa to enter the basement is: $basementPosition");
