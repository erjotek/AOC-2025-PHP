<?php

function input($input)
{
    $lines = explode(",", $input);
    $lines = array_map(fn($l) => explode('-', $l), $lines);

    return $lines;
}

function sumReps($input, $part2 = false)
{
    $sum = 0;

    $patt = '/^([1-9]\d*)(\1)' . ($part2 ? '+' : '') . '$/';

    foreach ($input as $l) {
        for ($i = $l[0]; $i <= $l[1]; $i++) {
            if (preg_match($patt, $i, $ret)) {
                $sum += $ret[0];
            }
        }
    }

    return $sum;
}

function part1($input)
{
    return sumReps($input); //13108371860
}

function part2($input)
{
    return sumReps($input, true); //22471660255
}


include __DIR__ . '/template.php';
