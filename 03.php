<?php

function input($input)
{
    $lines = explode("\n", $input);
    $lines = array_map(fn($l) => str_split($l), $lines);

    return $lines;
}

function maxNumber($input, $size)
{
    $sum = 0;
    foreach ($input as $line) {
        $nr = '';

        $pos = 0;
        for ($i = $size; $i > 0; $i--) {
            $last = -($i - 1) ?: null;
            $temp = array_slice($line, $pos, $last);
            $max = max($temp);
            $nr .= $max;
            $pos = array_search($max, $temp) + $pos + 1;
        }

        $sum += $nr;
    }

    return $sum;
}

function part1($input)
{
    return maxNumber($input, 2); //17432
}

function part2($input)
{
    return maxNumber($input, 12); // 173065202451341
}

include __DIR__ . '/template.php';
