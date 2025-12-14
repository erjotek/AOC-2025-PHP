<?php

function input($input)
{
    $lines = explode("\n", $input);
    $lines = array_map(fn($l) => array_map('intval', explode(',', $l)), $lines);
    return $lines;
}

function part1(array $input)
{
    $c = count($input);
    $max = 0;
    for ($i = 0; $i < $c - 1; $i++) {
        $x1 = $input[$i][0];
        $y1 = $input[$i][1];
        for ($j = $i + 1; $j < $c; $j++) {
            $h = abs($x1 - $input[$j][0]) + 1;
            $w = abs($y1 - $input[$j][1]) + 1;

            $max = max($max, $h * $w);
        }
    }

    return $max; //4782896435
}

function part2($input)
{
    $c = count($input);

    $shrinkX = shrinkAxis($input, 0);
    $shrinkY = shrinkAxis($input, 1);

    $shrunk = [];
    foreach ($input as [$x, $y]) {
        $shrunk[] = [$shrinkX['map'][(int)$x], $shrinkY['map'][(int)$y]];
    }

    $border = getBorder($shrunk);

    $outter = getOutter($shrinkX['size'], $shrinkY['size'], $border);

    $max = 0;
    for ($i = 0; $i < $c - 1; $i++) {
        for ($j = $i + 1; $j < $c; $j++) {
            [$i0, $i1] = $input[$i];
            [$j0, $j1] = $input[$j];

            $h = abs($i0 - $j0) + 1;
            $w = abs($i1 - $j1) + 1;

            if ($h * $w <= $max) {
                continue;
            }

            // shrunk: [x, y], outter: [y][x]
            [$six, $siy] = $shrunk[$i];
            [$sjx, $sjy] = $shrunk[$j];

            $maxX = max($six, $sjx);
            $minX = min($six, $sjx);
            for ($x = $minX; $x <= $maxX; $x++) {
                if (isset($outter[$siy][$x]) || isset($outter[$sjy][$x])) {
                    continue 2;
                }
            }
            $maxY = max($siy, $sjy);
            $minY = min($siy, $sjy);
            for ($y = $minY; $y <= $maxY; $y++) {
                if (isset($outter[$y][$six]) || isset($outter[$y][$sjx])) {
                    continue 2;
                }
            }

            $max = $h * $w;
        }
    }

    return $max; //1540060480

}

function shrinkAxis(array $tiles, int $index): array
{
    $axis = [];
    $min = $max = null;

    foreach ($tiles as $tile) {
        $value = (int) $tile[$index];
        $axis[] = $value;
        $min = $min === null ? $value : min($min, $value);
        $max = $max === null ? $value : max($max, $value);
    }

    $axis[] = $min - 1;
    $axis[] = $max + 1;

    sort($axis, SORT_NUMERIC);
    $unique = array_values(array_unique($axis));

    $map = [];
    foreach ($unique as $i => $value) {
        $map[$value] = $i;
    }

    return ['map' => $map, 'size' => count($unique), 'reverse' => $unique];
}

function getOutter($width, $height, array $border): array
{
    $outter = [];
    $queue = new SplQueue();
    $queue->enqueue([0, 0]);
    $outter[0][0] = true;

    $dirs = [[-1, 0], [1, 0], [0, 1], [0, -1]];

    while (!$queue->isEmpty()) {
        [$x, $y] = $queue->dequeue();

        foreach ($dirs as [$dx, $dy]) {
            $nx = $x + $dx;
            $ny = $y + $dy;

            if ($nx < 0 || $ny < 0 || $nx >= $width || $ny >= $height) {
                continue;
            }

            if (isset($outter[$ny][$nx]) || isset($border[$ny][$nx])) {
                continue;
            }

            $outter[$ny][$nx] = true;
            $queue->enqueue([$nx, $ny]);
        }
    }
    return $outter;
}

function getBorder(array $shrunk): array
{
    $border = [];
    $c = count($shrunk);
    for ($a = 0; $a < $c; $a++) {
        [$ax, $ay] = $shrunk[$a];
        [$bx, $by] = $shrunk[($a + 1) % $c];

        if ($ax == $bx) {
            for ($r = min($ay, $by); $r <= max($ay, $by); $r++) {
                $border[$r][$ax] = true;
            }
        } else {
            for ($col = min($ax, $bx); $col <= max($ax, $bx); $col++) {
                $border[$ay][$col] = true;
            }
        }
    }
    return $border;
}

include __DIR__ . '/template.php';
