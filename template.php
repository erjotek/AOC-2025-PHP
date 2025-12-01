<?php

ini_set('memory_limit', '4G');

function _display(int $part): void
{
    global $argv;
    $day = basename($argv[0], '.php');

    memory_reset_peak_usage();

    $startTs = hrtime(true);
    $func = 'part' . $part;

    echo "== Day $day part $part ==" . PHP_EOL;
    echo 'Result: ' . $func(input(rtrim(file_get_contents(__DIR__ . "/inputs/$day.txt")))) . PHP_EOL;
    echo 'Time  : ' . sprintf('%.6f', (hrtime(true) - $startTs) / 1e+9) . ' s' . PHP_EOL;
    echo 'Memory: ' . sprintf('%.5f', (memory_get_peak_usage() / 1024 ** 2)) . ' MB' . PHP_EOL;
    echo PHP_EOL;
}

/** @uses part1 */
_display(1);
/** @uses part2 */
_display(2);
