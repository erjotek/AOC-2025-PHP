<?php

function input($input)
{
    $lines = explode("\n", $input);
    $conns = [];

    foreach ($lines as $line) {
        $line = explode(": ", $line);
        $conns[$line[0]] = explode(" ", $line[1]);
    }

    return $conns;
}

function part1($conns)
{
    return conns('you', $conns); // 634
}

function part2($conns)
{
    return conns('svr', $conns); //377452269415704
}

function conns($start, $conns, $fft = false, $dac = false)
{
    static $cache;

    if ($start === 'out') {
        return $fft && $dac;
    }

    if ($start === 'fft') {
        $fft = true;
    }

    if ($start === 'dac') {
        $dac = true;
    }

    $c = 0;
    foreach ($conns[$start] as $next) {
        $key = "$next-$dac-$fft";
        if (!isset($cache[$key])) {
            $cache[$key] = conns($next, $conns, $fft, $dac);
        }
        $c += $cache[$key];
    }

    return $c;
}

include __DIR__ . '/template.php';
