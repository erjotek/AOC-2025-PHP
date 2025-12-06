<?php

function input($input)
{
    $lines = explode("\n", $input);

    return $lines;
}

function part1($input)
{
    $input = array_map(fn($l) => preg_split('/\s+/', $l), $input);

    $func = array_pop($input);

    $sum = 0;
    foreach ($func as $id => $op) {
        $nums = array_column($input, $id);
        $sum += $op === '*' ? array_product($nums) : array_sum($nums);
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
    for ($id = 0; $id <= $max; $id++) {
        $op = !empty(trim($func[$id]??'')) ?  $func[$id] : $op;

        $num = implode('', array_column($input, $id));

        if (trim($num) === '') {
            $sum += $op === '*' ? array_product($nums) : array_sum($nums);
            $nums = [];
            continue;
        }

        $nums[] = trim($num);
    }

    return $sum; //8486156119946
}

include __DIR__ . '/template.php';
