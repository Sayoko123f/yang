<?php
$inputFilename = isset($argv[1]) ? $argv[1] : "src.csv";
$resultDir = "./";
if (isset($argv[1])) {
    $resultDir = "./" . substr($argv[1], 0, strrpos($argv[1], '.'))."/";
    if (!is_dir($resultDir)) {
        mkdir($resultDir);
    }
}
$handle = fopen($inputFilename, "r");
if (!$handle) {
    die("Error: Failed to opening the file $inputFilename :(");
}
/*
fo1 = string mb_len is 1
fo2 = string mb_len is 2
fo3 = string mb_len is 3
fo4 = string mb_len is not 1,2,3
fo5 = string is failed myfilter($str)
*/
$fo1 = fopen($resultDir . "out1.csv", "x");
$fo2 = fopen($resultDir . "out2.csv", "x");
$fo3 = fopen($resultDir . "out3.csv", "x");
$fo4 = fopen($resultDir . "out4.csv", "x");
$fo5 = fopen($resultDir . "out5.csv", "x");

$utf8_with_bom = chr(239) . chr(187) . chr(191);

fwrite($fo1, $utf8_with_bom);
fwrite($fo2, $utf8_with_bom);
fwrite($fo3, $utf8_with_bom);
fwrite($fo4, $utf8_with_bom);
fwrite($fo5, $utf8_with_bom);

while (($line = fgets($handle)) !== false) {
    $fields = explode(",", $line);
    if (!myfliter($fields[0])) {
        fwrite($fo5, implode(",", $fields));
        continue;
    }
    $len = mb_strlen($fields[0], 'utf-8') - 2;

    switch ($len) {
        case 1:
            fwrite($fo1, implode(",", $fields));
            break;
        case 2:
            fwrite($fo2, implode(",", $fields));
            break;
        case 3:
            fwrite($fo3, implode(",", $fields));
            break;
        default:
            fwrite($fo4, implode(",", $fields));
            break;
    }
}
fclose($handle);
fclose($fo1);
fclose($fo2);
fclose($fo3);
fclose($fo4);
fclose($fo5);

function myfliter($str)
{
    $tmp = trim($str, "\"");
    if (preg_match("/(?!\p{Han}+).+/u", $tmp)) {
        return false;
    }
    return true;
}
