<?php

function input($input)
{
    $lines = explode("\n", $input);

    return $lines;
}

function part1($input)
{
    $input = array_map(function ($l) {
        preg_match_all('/\S+/', $l, $ret);
        return $ret[0];
    }, $input);

    $func = array_pop($input);

    $sum = 0;
    foreach ($func as $id => $op) {
        if ($op === '*') {
            $sum += array_product(array_column($input, $id));
        } else {
            $sum += array_sum(array_column($input, $id));
        }
    }

    return $sum; //4951502530386
}

function part2($input)
{
    $input = array_map(fn($l) => str_split($l), $input);

    $func = array_pop($input);

    $max = max(array_map(fn($l) => count($l), $input));
    $nums = [];
    $sum = 0;
    $lastFunc = '';
    for ($id = 0; $id <= $max; $id++) {
        $op = $func[$id] ?? '';
        if (!empty(trim($op))) {
            $lastFunc = $op;
        }

        $num = implode('', array_column($input, $id));

        if (trim($num) === '') {
            if ($lastFunc === '*') {
                $sum += array_product($nums);
            } else {
                $sum += array_sum($nums);
            }
            $nums = [];
            $lastFunc = '';
            continue;
        }

        $nums[] = trim($num);
    }

    return $sum; //8486156119946
}

include __DIR__ . '/template.php';
