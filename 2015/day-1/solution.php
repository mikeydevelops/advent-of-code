<?php

require_once __DIR__ . '/../../common.php';

$input = getInput();

$floor = 0;

foreach (str_split($input) as $instruction) {
    if ($instruction == '(') {
        $floor ++;

        continue;
    }

    if ($instruction == ')') {
        $floor --;

        continue;
    }
}

print("Santa will need to go to floor: $floor");
