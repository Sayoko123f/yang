<?php
$json = fopen('Q2_A.json', 'r');
$contentA = json_decode(fread($json, filesize('Q2_A.json')));
fclose($json);
$json = fopen('Q2_B.json', 'r');
$contentB = json_decode(fread($json, filesize('Q2_B.json')));
fclose($json);

$csv = fopen('output/overlapRate_Q2.csv', 'w');
$fieldlist = array('overlapRate', 'A', 'B');
fputcsv($csv, $fieldlist);
$years = range(99, 109);
for ($i = 0; $i < 11; $i++) {
    $year = strval($years[$i]);
    $row = array($year, $contentA->overlapRate->$year, $contentB->overlapRate->$year);
    fputcsv($csv,$row);
}
fclose($csv);
