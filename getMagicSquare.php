<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$result = Array();
$result['dobavochnye'] = array();
$result['selectedConstants'] = array();
$result['n'] = $_REQUEST['n'];
// $inArr=explode(',',$_REQUEST['in']);
$inArr = $_REQUEST['in'];
foreach ($inArr as $i => $v) {
    if ($v == '') {
        unset($inArr[$i]);
    }
}
sort($inArr);
$mag = $_REQUEST['mag'];
if (isset($_REQUEST['otherD'])) {
    $otherD = $_REQUEST['otherD'];
    foreach ($_REQUEST['otherD'] as $tai => $tav) {
        if (!in_array($tav, $result['dobavochnye'])) {
            $result['dobavochnye'][] = $tav;
        }
    }
} else {
    $otherD = NULL;
}
if (isset($_REQUEST['konstanty'])) {
    $konstanty = $_REQUEST['konstanty'];
    foreach ($_REQUEST['konstanty'] as $tai => $tav) {
        if (!in_array($tav, $result['selectedConstants'])) {
            $result['selectedConstants'][] = $tav;
        }
    }
} else {
    $konstanty = NULL;
}
$M = ($result['n'] * ($result['n'] * $result['n'] + 1)) / 2;
if (is_array($inArr) && is_array($otherD)) {
    $inArr = array_merge($inArr, $otherD); // обьединение искомых и добавочных
}

$line = 0;
$result['spp'] = 6 * 6;
$count_line = 300000;
$result['add'] = $otherD;
$result['in'] = $inArr;
$result['M'] = $M;

$result['constants'] = array();
if (isset($_REQUEST['cp'])) {
    $result['cp'] = $_REQUEST['cp'];
} else {
    $result['cp'] = 1;
}
$offsets = array();
$kvadraty = array();
$kvadraty = file("{$result['n']}.variants");
$result['ac'] = (count($kvadraty) - 1);
foreach ($kvadraty as $ki => $kv) {
    $count = 0;
    $testArr = explode(',', trim($kv));
    $tArr = $testArr;
    foreach ($inArr as $ii => $iv) {
        if (in_array($iv, $tArr)) {
            $count++;
            if (($key = array_search($iv, $tArr)) !== false) {
                unset($tArr[$key]);
                sort($tArr);
            }
        } else {
            break;
        }
    }
    if ($count == count($inArr)) {
        $const = 0;
        for ($i = 1; $i <= $result['n']; $i++) {
            $const+=$testArr[$i - 1];
        }
        if (!in_array($const, $result['constants'])) {
            $result['constants'][] = $const;
        }
        if ($mag == 'true' && $const != $M) {
            continue;
        }
        if (isset($konstanty)) {
            $k_ok = false;
            foreach ($konstanty as $i => $v) {
                if ($const == $v) {
                    $k_ok = true;
                }
            }
            if (!$k_ok) {
                continue;
            }
        }
        $offsets[] = $ki;
    }
}


$result['count'] = count($offsets);
$result['pc'] = ceil($result['count'] / $result['spp']);


//

$kvadraty2 = Array();
for ($active_offset = (($result['cp'] - 1) * $result['spp']); ($active_offset < ($result['spp'] * $result['cp'])) && ($active_offset < $result['count']); $active_offset++) {
    $kvadraty2[] = $kvadraty[$offsets[$active_offset]];
}
//print_r($kvadraty2);
//$line = $count_line * $result['cp'];
//$stream->SetOffset($line);
//$kvadraty = $stream->Read($count_line);
foreach ($kvadraty2 as $i => $v) {
    $count = 0;
    $testArr = explode(',', trim($v));
    $tArr = $testArr;
    foreach ($inArr as $ii => $iv) {
        if (in_array($iv, $tArr)) {
            $count++;
            if (($key = array_search($iv, $tArr)) !== false) {
                unset($tArr[$key]);
                sort($tArr);
            }
        } else {
            break;
        }
    }
    if ($count == count($inArr)) {
        $const = 0;
        for ($i = 1; $i <= $result['n']; $i++) {
            $const+=$testArr[$i - 1];
        }
        if ($mag == 'true' && $const != $M) {
            continue;
        }
        if (isset($konstanty)) {
            $k_ok = false;
            foreach ($konstanty as $i => $v) {
                if ($const == $v) {
                    $k_ok = true;
                }
            }
            if (!$k_ok) {
                continue;
            }
        }
        $result['results'][] = Array(
            "s" => $testArr,
            "m" => $const,
            "a" => $tArr,
        );
        foreach ($tArr as $tai => $tav) {
            if (!in_array($tav, $result['dobavochnye'])) {
                $result['dobavochnye'][] = $tav;
            }
        }
    }
}
sort($result['dobavochnye']);
echo json_encode($result);
?>