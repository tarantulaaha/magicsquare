<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//define('UIN',693469329);
//define('PASSWORD','tar1205');
umask(0);
//require_once 'icq.php';
$time = time();
$arr = array(
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    10,
    11,
    12,
    13,
    14,
    15,
    16,
    17,
    18,
    19,
    20,
    21,
    22,
    23,
    24
);

$n = 4;
$M = ($n * ($n * $n + 1)) / 2;
$Mmax = 0;
if (isset($_REQUEST['in'])) {
    $inArr = $_REQUEST['in'];
} else {
    $inArr = $arr;
}
foreach ($inArr as $i => $v) {
    $Mmax += $v;
}
$Mmax = ceil($Mmax / count($inArr)) * $n;

$arrlen = count($arr);
sort($inArr);

echo "Построение матрицы {$n}x{$n}<br>\n";
echo "Магическая константа нормального магического квадрата: {$M}<br>\n";
$inStr = implode(',', $inArr);
echo "Вход: {$inStr}<br>\n";
$testArr = array();
$kvadratov = 0;
$combinations = array();
if (file_exists(__DIR__ . "/{$n}.variants")) {
    $combinations = file(__DIR__ . "/{$n}.variants");
    $kvadratov = count($combinations);
}

function testRows($attr)
{
    global $M, $n;
    $rows = floor(count($attr) / $n);
    $cell = 0;
    for ($ri = 1; $ri <= $rows; $ri++) {
        $r = 0;
        for ($x = 0; $x < $n; $x++) {
            $r += $attr[$cell];
            $cell++;
        }
        if ($r != $M) {
            return false;
        }
    }
    return true;
}

function testCols($attr)
{
    global $M, $n;
    $cols = count($attr) - (($n * $n) - $n);
    for ($ci = 1; $ci <= $cols; $ci++) {
        $cell = $ci - 1;
        $c = 0;
        for ($x = 0; $x < $n; $x++) {
            $c += $attr[$cell];
            $cell += $n;
        }
        if ($c != $M) {
            return false;
        }
    }
    return true;
}

function recurs($ar, $pos)
{
    global $testArr, $n, $kvadratov, $combinations, $inStr, $M;
    $pos++;
    $sdvig = 0;
    $lastsdvig = 0;
    if (file_exists(__DIR__ . "/{$n}_{$M}_{$inStr}_{$pos}_sdvig")) {
        $lastsdvig = file_get_contents(__DIR__ . "/{$n}_{$M}_{$inStr}_{$pos}_sdvig");
    }
    for ($i = 1; $i <= count($ar); $i++) {
        unset($tmpArr);
        $tmpArr = $ar;
        array_splice($testArr, $pos, count($testArr));
        $testArr[] = $tmpArr[$i - 1];
        unset($tmpArr[$i - 1]);
        sort($tmpArr);
        $tas = implode('/', $testArr);
        $sdvig++;
        if ($lastsdvig > $sdvig) {
            continue;
        }
        if ($pos <= $n - 1) {
            file_put_contents(__DIR__ . "/{$n}_{$M}_{$inStr}_{$pos}_sdvig", $sdvig);
        }
        if (!testRows($testArr)) {
            continue;
        }
        if (!testCols($testArr)) {
            continue;
        }
        if ($pos == (($n * $n) - 1)) {
            if (in_array(implode(',', $testArr) . "\n", $combinations)) {
                continue;
            }
            if (test($testArr)) {
                $kvadratov++;
                file_put_contents(__DIR__ . "/{$n}.variants", implode(',', $testArr) . "\n", FILE_APPEND | LOCK_EX);
                echo date("d.m.Y H:i:s") . " M:{$M} c:{$kvadratov} {$tas}\n";
                if (file_exists(__DIR__ . "/{$n}.variants")) {
                    $combinations = file(__DIR__ . "/{$n}.variants");
                    $kvadratov = count($combinations);
                }
            }
        }

        if ($pos < (($n * $n) - 1)) {
            recurs($tmpArr, $pos);
        }
        for ($posi = $pos + 1; $posi < ($n * $n); $posi++) {
            if (file_exists(__DIR__ . "/{$n}_{$M}_{$inStr}_{$posi}_sdvig")) {
                unlink(__DIR__ . "/{$n}_{$M}_{$inStr}_{$posi}_sdvig");
            }
        }
    }
}

$loaded = false;
for ($M = $M; $M < $Mmax; $M++) {
    if (!$loaded) {
        if (file_exists(__DIR__ . "/{$n}_last")) {
            $M = trim(file_get_contents(__DIR__ . "/{$n}_last"));
            $loaded = true;
        }
    }
    echo "{$M}\n";
    file_put_contents(__DIR__ . "/{$n}_last", $M);
    #------------
    $cutArr = $inArr;
    array_splice($cutArr, ceil((($M * 2) / $n) - 1), count($cutArr));
    $inStr = implode(',', $cutArr);
    recurs($cutArr, -1);
    #----------------
    //recurs($inArr,-1); //
}

function test($inArr)
{
    global $M, $n;
    $d1 = 0;
    for ($x = 1; $x <= $n; $x++) {
        if (isset($inArr[(($x - 1) * $n) + ($x - 1)])) {
            $d1 += $inArr[(($x - 1) * $n) + ($x - 1)];
        } else {
            return false;
        }
    }
    $d2 = 0;
    for ($x = 1; $x <= $n; $x++) {
        if (isset($inArr[(($x - 1) * $n) + ($n - $x)])) {
            $d2 += $inArr[(($x - 1) * $n) + ($n - $x)];
        } else {
            return false;
        }
    }
    if ($d1 != $M) {
        return false;
    }
    if ($d2 != $M) {
        return false;
    }
    return true;
}

?>