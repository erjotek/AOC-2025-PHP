<?php

$froms = [];
$ends = [];

function input($input)
{
    $lines = explode("\n", $input);
    $lines = array_map(fn($l) => str_split($l), $lines);

    return $lines;
}

function part1($input)
{
    global $froms;
    global $ends;

    $starts = [];
    $id = array_search('S', $input[0]);
    $starts["0-$id"] = [0, $id];
    $input[0][$id] = '.';
    $breams = [];

    $max = count($input);

    $change = true;
    while ($change) {
        $change = false;
        foreach ($starts as $sid => [$row, $col]) {
            while (true) {
                if ($input[$row][$col] === '.') {
                    $row++;
                }

                if ($row >= $max) {
                    $froms["$row-$col"][$sid] = $sid;
                    $ends["$row-$col"] = true;
                    break;
                }

                if ($input[$row][$col] === '^') {
                    $froms["$row-$col"][$sid] = $sid;

                    if (!isset($breams["$row-$col"])) {
                        $starts["$row-$col-l"] = [$row, $col - 1];
                        $starts["$row-$col-r"] = [$row, $col + 1];
                        $breams["$row-$col"] = true;
                        $change = true;
                    }
                    break;
                }
            }
        }
    }

    return count($breams); //1533
}

function part2($input)
{
    global $ends;
    $sum = 0;
    foreach ($ends as $end => $_) {
        $paths = findParent($end);
        $sum += $paths;
    }

    return $sum; //10733529153890
}

function findParent($end)
{
    static $cache;
    global $froms;

    if (!isset($froms[$end])) {
        return 1;
    }

    $paths = 0;
    foreach ($froms[$end] as $ending) {
        if (empty($cache[$ending])) {
            $pp = findParent(substr($ending, 0, -2));
            $cache[$ending] = $pp;
        } else {
            $pp = $cache[$ending];
        }
        $paths += $pp;
    }

    return $paths;
}

include __DIR__ . '/template.php';
