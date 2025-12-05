<?php

function input($input)
{
    $lines = explode("\n\n", $input);
    $lines[0] = explode("\n", $lines[0]);
    $lines[1] = explode("\n", $lines[1]);
    sort($lines[0]);
    sort($lines[1]);

    return $lines;
}

function part1($input)
{
    $ranges = packId($input[0]);

    $valid = 0;
    foreach ($input[1] as $ind) {
        foreach ($ranges as [$from, $to]) {
            if ($ind >= $from && $ind <= $to) {
                $valid++;
                continue 2;
            }
        }
    }

    return $valid; //798
}

function part2($input)
{
    $ranges = packId($input[0]);

    $sum = 0;
    foreach ($ranges as [$from, $to]) {
        $sum += ($to - $from) +1;
    }

    return $sum; // 366181852921027
}

function packId($input): array
{
    $ranges = [];

    foreach ($input as $line) {
        [$from, $to] = explode('-', $line);
        $ranges[$from] = max($to, $ranges[$from] ?? 0);
    }

    ksort($ranges);

    $valid = [];
    $lastTo = 0;
    $lastFrom = 0;
    foreach ($ranges as $from => $to) {
        if ($lastTo < $from) {
            $valid[] = [$from, $to];
            $lastFrom = $from;
            $lastTo = $to;
            continue;
        }

        if ($lastFrom <= $from && $lastTo <= $to) {
            [$lastFrom, $lastTo] = array_pop($valid);
            $valid[] = [$lastFrom, $to];
            $lastTo = $to;
            continue;
        }

        if ($lastTo > $from && $lastTo <= $to) {
            [$lastFrom, $lastTo] = array_pop($valid);
            $valid[] = [$lastFrom, $to];
            $lastTo = $to;
            continue;
        }
    }
    return $valid;
}

include __DIR__ . '/template.php';
