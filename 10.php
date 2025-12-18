<?php

function input($input)
{
    $lines = explode("\n", $input);
    $lines = array_map(fn($l) => explode(" ", $l), $lines);

    return $lines;
}

function part1($input)
{
    $sum = 0;
    foreach ($input as $nr => $line) {
        $sum += countPress($line);
    }
    return $sum; //461
}

function part2($input)
{
    $sum = 0;
    foreach ($input as $nr => $line) {
        $sum += countGauss($line);
    }
    return $sum; // 16386
}

function countPress($line)
{
    $goal = str_split(str_replace([".", "#"], [0, 1], trim($line[0], "[]")));
    array_pop($line);
    unset($line[0]);

    $opts = [];
    foreach ($line as $opt) {
        $opt = explode(',', trim($opt, "()"));
        $opts[] = $opt;
    }

    $min = 10000000;
    $start = array_fill(0, count($goal), 0);

    $q = new SplPriorityQueue();
    $q->insert([$start, 0], 0);
    $visited = [];

    $max = max($goal);

    $total = 0;

    while (!$q->isEmpty()) {
        [$state, $steps] = $q->extract();

        if (isset($visited[implode('', $state)]) && $visited[implode('', $state)] < $steps) {
            continue;
        }


        $total++;
        foreach ($opts as $opt) {
            $newstate = $state;

            if ($steps + 1 >= $min) {
                continue;
            }

            for ($i = 0; $i < count($opt); $i++) {
                $newstate[$opt[$i]] = ($state[$opt[$i]] + 1) % 2;
            }

            if ($newstate == $goal) {
                $min = min($min, $steps + 1);
                continue 2;
            }


            if ($steps < $min && !isset($visited[implode('', $newstate)])) {
                $visited[implode('', $newstate)] = $steps + 1;
                $newcost = $steps + 1;

                foreach ($goal as $gi => $val) {
                    $newcost += $val - $newstate[$gi];
                }
                $q->insert([$newstate, $steps + 1], -$newcost);
            }
        }
    }

    return $min;
}

function countGauss($line)
{
    $goal = array_pop($line);
    unset($line[0]);
    $goal = explode(',', trim($goal, "{}"));

    $opts = [];
    foreach ($line as $opt) {
        $opt = explode(',', trim($opt, "()"));
        $opt = array_combine($opt, $opt);
        $opts[] = $opt;
    }

    $matrix = [];

    foreach ($goal as $pos => $val) {
        $row = [];
        foreach ($opts as $opt) {
            $row[] = isset($opt[$pos]) ? 1 : 0;
        }

        $row[] = $val;
        $matrix[] = $row;
    }

    return gaussCalc($matrix);
}


function gaussCalc(array $matrix)
{
    $m = count($matrix);
    $n = count($matrix[0]) - 1;

    // Oblicz dynamiczne granice dla każdej zmiennej (kolumny)
    $limits = array_fill(0, $n, PHP_INT_MAX);
    for ($row = 0; $row < $m; $row++) {
        $rhs = $matrix[$row][$n];
        for ($col = 0; $col < $n; $col++) {
            if ($matrix[$row][$col] === 1) {
                $limits[$col] = min($limits[$col], $rhs);
            }
        }
    }

    // Eliminacja Gaussa z normalizacją wierszy (algorytm z 10.rust.php)
    $previous = range(0, $n - 1);
    $current = [];
    $pivotCols = [];

    while ($previous !== $current) {
        $previous = $current;
        $current = range(0, $n - 1);
        $pivotCols = [];

        $pivotRow = 0;
        $pivotCol = 0;

        while ($pivotRow < $m && $pivotCol < $n) {
            // Znajdź wiersz z niezerowym współczynnikiem który dzieli cały wiersz
            $found = null;
            for ($r = $pivotRow; $r < $m; $r++) {
                $coefficient = $matrix[$r][$pivotCol];
                if ($coefficient !== 0) {
                    $allDivisible = true;
                    for ($c = 0; $c <= $n; $c++) {
                        if ($matrix[$r][$c] % $coefficient !== 0) {
                            $allDivisible = false;
                            break;
                        }
                    }
                    if ($allDivisible) {
                        $found = $r;
                        break;
                    }
                }
            }

            if ($found === null) {
                $pivotCol++;
                continue;
            }

            // Zamień wiersze
            [$matrix[$pivotRow], $matrix[$found]] = [$matrix[$found], $matrix[$pivotRow]];

            // Normalizuj wiersz pivotowy
            $coefficient = $matrix[$pivotRow][$pivotCol];
            for ($c = 0; $c <= $n; $c++) {
                $matrix[$pivotRow][$c] = intdiv($matrix[$pivotRow][$c], $coefficient);
            }

            // Eliminuj z innych wierszy
            for ($row = 0; $row < $m; $row++) {
                if ($row !== $pivotRow) {
                    $coef = $matrix[$row][$pivotCol];
                    for ($c = 0; $c <= $n; $c++) {
                        $matrix[$row][$c] -= $coef * $matrix[$pivotRow][$c];
                    }
                }
            }

            // Usuń kolumnę z current (to nie jest zmienna swobodna)
            $current = array_values(array_diff($current, [$pivotCol]));
            $pivotCols[] = $pivotCol;
            $pivotRow++;
            $pivotCol++;
        }
    }

    $rank = count($pivotCols);

// --- Sprawdź sprzeczność ---

    for ($r = 0; $r < $m; $r++) {
        $allZero = true;
        for ($c = 0; $c < $n; $c++) {
            if ($matrix[$r][$c] !== 0) {
                $allZero = false;
                break;
            }
        }

        if ($allZero && $matrix[$r][$n] !== 0) {
            echo "Brak rozwiązań (układ sprzeczny).\n";
            exit(0);
        }
    }

// --- Rozwiązanie jednoznaczne ---

    if ($rank === $n) {
        $x = [];
        for ($i = $n - 1; $i >= 0; $i--) {
            $sum = $matrix[$i][$n];
            for ($j = $i + 1; $j < $n; $j++) {
                $sum -= $matrix[$i][$j] * $x[$j];
            }
            $x[$i] = intdiv($sum, $matrix[$i][$i]);
        }

        ksort($x);

//        echo "Rozwiązanie:\n";
        $total = 0;
        foreach ($x as $i => $val) {
//            echo 'x' . ($i + 1) . ' = ' . $val . "\n";
            $total += $val;
        }

        return $total;
//        echo "Suma: $total\n";
//        exit(0);
    }

// --- Nieskończenie wiele rozwiązań ---
// Używamy parametryzacji: zmienne swobodne to parametry, szukamy zakresu dla x_i >= 0

    $freeVars = array_values(array_diff(range(0, $n - 1), $pivotCols));
    $numFree = count($freeVars);

    // Próbuj rozwiązać przez parametryzację zmiennych swobodnych
    if ($numFree > 0) {
        $result = solveWithFreeVars($matrix, $pivotCols, $freeVars, $n, $rank, $limits);
        if ($result !== null) {
            return $result;
        }
    }

    // Fallback: enumeracja baz (dla przypadków z wieloma zmiennymi swobodnymi)
    $best = null;
    $bestSum = PHP_INT_MAX;

    foreach (combinations($n, $rank) as $basis) {
        $sub = [];
        $rhs = [];

        for ($i = 0; $i < $rank; $i++) {
            $subRow = [];
            foreach ($basis as $colIdx) {
                $subRow[] = $matrix[$i][$colIdx];
            }
            $sub[] = $subRow;
            $rhs[] = $matrix[$i][$n];
        }

        $xB = solveSquareInt($sub, $rhs);
        if ($xB === null) {
            continue;
        }

        // Sprawdź czy wszystkie >= 0
        $ok = true;
        foreach ($xB as $val) {
            if ($val < 0) {
                $ok = false;
                break;
            }
        }

        if (!$ok) {
            continue;
        }

        $x = array_fill(0, $n, 0);
        for ($i = 0; $i < $rank; $i++) {
            $x[$basis[$i]] = $xB[$i];
        }

        $sum = array_sum($x);
        if ($sum < $bestSum) {
            $best = $x;
            $bestSum = $sum;
        }
    }

    if ($best === null) {
        print_r($matrix);
        echo "Brak rozwiązania nieujemnego.\n";
        exit(0);
    }

    return $bestSum;
}


// --- Funkcje pomocnicze ---

/**
 * Rozwiązuje układ z jedną lub więcej zmiennymi swobodnymi.
 * Zoptymalizowana wersja z dynamicznymi granicami i analitycznym rozwiązaniem ostatniej zmiennej.
 */
function solveWithFreeVars(array $matrix, array $pivotCols, array $freeVars, int $n, int $rank, array $limits): ?int
{
    $numFree = count($freeVars);
    $height = count($matrix);

    if ($numFree === 0) {
        return null;
    }

    // Oblicz bazową sumę z wierszy pivotowych (RHS)
    $basePresses = 0;
    for ($i = 0; $i < $rank; $i++) {
        $basePresses += $matrix[$i][$n];
    }

    // Oblicz koszt i współczynniki dla każdej zmiennej swobodnej
    $cost = [];
    $coefficients = [];
    $orderedLimit = [];

    foreach ($freeVars as $f => $fv) {
        $costSum = 0;
        for ($i = 0; $i < $rank; $i++) {
            $costSum += $matrix[$i][$fv];
        }
        $cost[$f] = 1 - $costSum;  // 1 za samą zmienną minus wpływ na zmienne bazowe
        $orderedLimit[$f] = $limits[$fv];

        $coefficients[$f] = [];
        for ($row = 0; $row < $height; $row++) {
            $coefficients[$f][$row] = $matrix[$row][$fv];
        }
    }

    // Przygotuj RHS dla rekurencji
    $rhs = array_fill(0, $numFree, array_fill(0, $height, 0));
    for ($row = 0; $row < $height; $row++) {
        $rhs[0][$row] = $matrix[$row][$n];
    }

    return solveRecurse($cost, $orderedLimit, $coefficients, $rhs, $rank, $basePresses, 0, $height, $numFree);
}

/**
 * Rekurencyjne przeszukiwanie zmiennych swobodnych z analitycznym rozwiązaniem ostatniej.
 */
function solveRecurse(
    array $cost,
    array $limit,
    array $coefficients,
    array &$rhs,
    int $fixed,
    int $presses,
    int $depth,
    int $height,
    int $free
): ?int {
    if ($depth === $free - 1) {
        // Ostatnia zmienna swobodna - oblicz granice analitycznie
        $lower = 0;
        $upper = $limit[$depth];

        // Sprawdź nierówności z wierszy pivotowych
        for ($row = 0; $row < $fixed; $row++) {
            $coef = $coefficients[$depth][$row];
            $r = $rhs[$depth][$row];

            if ($r >= 0) {
                if ($coef > 0) {
                    $upper = min($upper, intdiv($r, $coef));
                }
            } elseif ($coef < 0) {
                $floor = intdiv($r + $coef + 1, $coef);
                $lower = max($lower, $floor);
            } else {
                // coef == 0, r < 0 -> sprzeczność
                $upper = -1;
            }
        }

        // Sprawdź równości z wierszy niepivotowych (jeśli istnieją)
        for ($row = $fixed; $row < $height; $row++) {
            $c = $coefficients[$depth][$row];
            $r = $rhs[$depth][$row];

            if ($c !== 0) {
                if ($r % $c === 0) {
                    $val = intdiv($r, $c);
                    $upper = min($upper, $val);
                    $lower = max($lower, $val);
                } else {
                    $upper = -1;
                }
            } elseif ($r !== 0) {
                // c == 0 ale r != 0 -> sprzeczność
                $upper = -1;
            }
        }

        if ($lower > $upper) {
            return null;
        }

        // Wybierz lower lub upper w zależności od kosztu
        $finalPresses = $presses + $cost[$depth] * ($cost[$depth] >= 0 ? $lower : $upper);
        return $finalPresses;
    }

    // Rekurencyjne przeszukiwanie
    $minResult = null;

    for ($x = 0; $x <= $limit[$depth]; $x++) {
        $nextPresses = $presses + $x * $cost[$depth];

        // Aktualizuj RHS dla następnej głębokości
        for ($row = 0; $row < $height; $row++) {
            $rhs[$depth + 1][$row] = $rhs[$depth][$row] - $x * $coefficients[$depth][$row];
        }

        $result = solveRecurse($cost, $limit, $coefficients, $rhs, $fixed, $nextPresses, $depth + 1, $height, $free);

        if ($result !== null && ($minResult === null || $result < $minResult)) {
            $minResult = $result;
        }
    }

    return $minResult;
}

function gcdRow(array $row): int
{
    $g = 0;
    foreach ($row as $val) {
        $g = gcd(abs($val), $g);
    }
    return $g === 0 ? 1 : $g;
}

function gcd(int $a, int $b): int
{
    while ($b !== 0) {
        $tmp = $a % $b;
        $a = $b;
        $b = $tmp;
    }
    return $a === 0 ? 1 : $a;
}

function solveSquareInt(array $a, array $b): ?array
{
    $n = count($a);
    if ($n === 0) {
        return [];
    }

    $aug = [];
    for ($i = 0; $i < $n; $i++) {
        $aug[$i] = $a[$i];
        $aug[$i][] = $b[$i];
    }

    for ($col = 0; $col < $n; $col++) {
        $pivotRow = null;
        for ($r = $col; $r < $n; $r++) {
            if ($aug[$r][$col] !== 0) {
                $pivotRow = $r;
                break;
            }
        }

        if ($pivotRow === null) {
            return null;
        }

        if ($pivotRow !== $col) {
            [$aug[$col], $aug[$pivotRow]] = [$aug[$pivotRow], $aug[$col]];
        }

        for ($r = 0; $r < $n; $r++) {
            if ($r === $col || $aug[$r][$col] === 0) {
                continue;
            }

            $aa = $aug[$r][$col];
            $bb = $aug[$col][$col];

            for ($c = 0; $c <= $n; $c++) {
                $aug[$r][$c] = $aug[$r][$c] * $bb - $aug[$col][$c] * $aa;
            }

            $g = gcdRow($aug[$r]);
            if ($g > 1) {
                for ($c = 0; $c <= $n; $c++) {
                    $aug[$r][$c] = intdiv($aug[$r][$c], $g);
                }
            }
        }
    }

    $x = [];
    for ($i = 0; $i < $n; $i++) {
        if ($aug[$i][$n] % $aug[$i][$i] !== 0) {
            return null; // nie dzieli się bez reszty
        }
        $x[$i] = intdiv($aug[$i][$n], $aug[$i][$i]);
    }

    return $x;
}

function combinations(int $n, int $k): Generator
{
    if ($k < 0 || $k > $n) {
        return;
    }

    if ($k === 0) {
        yield [];
        return;
    }

    $indices = range(0, $k - 1);

    while (true) {
        yield $indices;

        $i = $k - 1;
        while ($i >= 0 && $indices[$i] === $i + $n - $k) {
            $i--;
        }

        if ($i < 0) {
            break;
        }

        $indices[$i]++;
        for ($j = $i + 1; $j < $k; $j++) {
            $indices[$j] = $indices[$j - 1] + 1;
        }
    }
}


include __DIR__ . '/template.php';
