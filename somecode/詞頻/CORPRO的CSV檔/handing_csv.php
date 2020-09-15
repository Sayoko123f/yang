<?php
$dir = "./";
$handle = fopen($dir . "src.csv", "r");
if (!$handle) {
    die("error opening the file src.csv");
}
/*
fo1 = string mb_len is 1
fo2 = string mb_len is 2
fo3 = string mb_len is 3
fo4 = string mb_len is not 1,2,3
fo5 = string is failed myfilter($str)
*/
$fo1 = fopen($dir . "out1.csv", "x");
$fo2 = fopen($dir . "out2.csv", "x");
$fo3 = fopen($dir . "out3.csv", "x");
$fo4 = fopen($dir . "out4.csv", "x");
$fo5 = fopen($dir . "out5.csv", "x");

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
