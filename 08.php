<?php

function input($input)
{
    $lines = explode("\n", $input);
    $lines = array_map(fn($l) => array_map('intval', explode(',', $l)), $lines);

    return $lines;
}

function calc($input, $part2 = false) {
    $deltas = [];

    $limit = $part2 ? null : 1000;

    for ($f = 0; $f < count($input) - 1; $f++) {
        for ($t = $f + 1; $t < count($input); $t++) {
            if ($f == $t) {
                continue;
            }
            $l = $input[$f];
            $r = $input[$t];
            $delta = (($l[0] - $r[0]) ** 2 + ($l[1] - $r[1]) ** 2 + ($l[2] - $r[2]) ** 2);
            $deltas["$f-$t"] = $delta;
        }
    }

    asort($deltas);

    $cir = 1;
    foreach (array_slice($deltas, 0, $limit) as $ids => $d) {
        $cir++;
        [$l, $r] = explode('-', $ids);

        $id1 = $input[$l][3] ?? $cir;
        $id2 = $input[$r][3] ?? $cir;

        foreach ($input as &$sock) {
            if (in_array($sock[3] ?? -1, [$id1, $id2])) {
                $sock[3] = $cir;
            }
        }
        unset($sock);

        $input[$l][3] = $cir;
        $input[$r][3] = $cir;

        if ($part2 && count(array_count_values(array_column($input, 3))) == 1 && count(array_column($input, 3)) == count($input)) {
            $ids = explode('-', $ids);
            return $input[$ids[0]][0] * $input[$ids[1]][0];
            break;
        }
    }

    $x = array_column($input, 3);
    $x = array_count_values($x);
    arsort($x);

    return array_product(array_slice($x, 0, 3));
}

function part1($input)
{
    return calc($input); //66640
}

function part2($input)
{
    return calc($input, true);    //78894156
}

include __DIR__ . '/template.php';
