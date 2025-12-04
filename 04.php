<?php

function input($input)
{
    $lines = explode("\n", $input);
    $lines = array_map(fn($l) => str_split($l), $lines);

    return $lines;
}

function removeRolls($input, $part2 = false)
{
    $dirs = [[-1, -1], [-1, 0], [-1, 1], [1, -1], [1, 0], [1, 1], [0, -1], [0, 1]];

    $sum = 0;
    while (true) {
        $newInput = $input;
        $change = false;
        foreach ($input as $rowId => $rows) {
            foreach ($rows as $colId => $char) {
                if ($char != '@') {
                    continue;
                }
                $c = 0;
                foreach ($dirs as [$nr, $nc]) {
                    if (($input[$rowId + $nr][$colId + $nc] ?? '.') === '@') {
                        $c++;
                    }
                }

                if ($c < 4) {
                    $newInput[$rowId][$colId] = '.';
                    $sum++;
                    $change = true;
                }
            }
        }

        $input = $newInput;
        if (!$change || !$part2) {
            break;
        }
    }

    return $sum;
}

function part1($input)
{
    return removeRolls($input); //1474
}

function part2($input)
{
    return removeRolls($input, true); //8910
}

include __DIR__ . '/template.php';
