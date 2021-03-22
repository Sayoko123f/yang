<?php
ini_set('memory_limit', '1024M');
if (!isset($argv[1], $argv[2])) {
    die('Please input two csv file.');
}
$str = file_get_contents($argv[1]);
if (!$str) {
    die("{$argv[1]}: file_get_contents error.");
}
$handle = fopen('php://memory', 'r+');
fwrite($handle, mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
rewind($handle);
$a = array();
$b = array();
$i = 0;
while ($line = fgetcsv($handle)) {
    if (++$i === 1) {
        continue;
    }
    $a[$line[0]] = array(mb_convert_encoding($line[1], 'UTF-8', 'HTML-ENTITIES'), mb_convert_encoding($line[2], 'UTF-8', 'HTML-ENTITIES'));
}
fclose($handle);
$str = file_get_contents($argv[2]);
if (!$str) {
    die("{$argv[2]}: file_get_contents error.");
}
$handle = fopen('php://memory', 'r+');
fwrite($handle, mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
rewind($handle);
$i = 0;
while ($line = fgetcsv($handle)) {
    if (++$i === 1) {
        continue;
    }
    $b[$line[0]] = array(mb_convert_encoding($line[1], 'UTF-8', 'HTML-ENTITIES'), mb_convert_encoding($line[2], 'UTF-8', 'HTML-ENTITIES'));
}
fclose($handle);
// End readfile
// Start clac
$result = array('intersection' => array(), 'subtraction' => array(array(), array()));
foreach ($a as $k => $v) {
    if (array_key_exists($k, $b)) {
        $appendKey = "({$a[$k][0]},{$b[$k][0]})({$a[$k][1]},{$b[$k][1]})";
        $result['intersection'][mb_convert_encoding($k, 'UTF-8', 'HTML-ENTITIES').$appendKey] = array(strval(intval($a[$k][0]) + intval($b[$k][0])), strval(intval($a[$k][1]) + intval($b[$k][1])));
    } else {
        $result['subtraction'][0][mb_convert_encoding($k, 'UTF-8', 'HTML-ENTITIES')] = array($a[$k][0], $a[$k][1]);
    }
}
foreach ($b as $k => $v) {
    if (!array_key_exists($k, $a)) {
        $result['subtraction'][1][mb_convert_encoding($k, 'UTF-8', 'HTML-ENTITIES')] = array($b[$k][0], $b[$k][1]);
    }
}
// End calc
// Start output result
$resultDir = './result/';
if(!is_dir($resultDir)){
    mkdir($resultDir);
}
$header = array('Word', 'termFreq', 'docFreq');
$handle = fopen("$resultDir{$argv[1]}∩{$argv[2]}.csv", 'w');
fputcsv($handle, $header);
foreach ($result['intersection'] as $k => $v) {
    $row = array($k, $v[0], $v[1]);
    fputcsv($handle, $row);
}
fclose($handle);



$handle = fopen("{$resultDir}a - {$argv[1]}∩{$argv[2]}.csv", 'w');
fputcsv($handle, $header);
foreach ($result['subtraction'][0] as $k => $v) {
    $row = array($k, $v[0], $v[1]);
    fputcsv($handle, $row);
}
fclose($handle);
$handle = fopen("{$resultDir}b - {$argv[1]}∩{$argv[2]}.csv", 'w');
fputcsv($handle, $header);
foreach ($result['subtraction'][1] as $k => $v) {
    $row = array($k, $v[0], $v[1]);
    fputcsv($handle, $row);
}
fclose($handle);
