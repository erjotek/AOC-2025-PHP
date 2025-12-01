<?php

function input($input)
{
    $lines = explode("\n", $input);

    return $lines;
}

function countRot($input, $part2 = false)
{
    $pos = 50;
    $count = 0;

    $lastpos = 50;
    foreach ($input as $n) {
        $dir = $n[0] == 'L' ? -1 : 1;
        $n = substr($n, 1);

        $nd = (int)($n / 100);
        $nr = $n % 100;
        if ($part2) {
            $count += $nd;
        }

        $lastpos = $pos;
        $pos += $dir * $nr;

        if ($pos >= 100) {
            $pos = $pos % 100;
            if ($lastpos && $part2) {
                $count++;
                continue;
            }
        }

        if ($pos < 0) {
            $pos = 100 + $pos;
            if ($lastpos && $part2) {
                $count++;
                continue;
            }
        }

        if (!$pos) {
            $count++;
        }
    }

    return $count; //1123
}

function part1($input)
{
    return countRot($input); //1123
}

function part2($input)
{
    return countRot($input, true); //6695
}

include __DIR__ . '/template.php';
