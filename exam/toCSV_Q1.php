<?php
$json = fopen('Q1.json', 'r');
$content = json_decode(fread($json, filesize('Q1.json')));
fclose($json);

$csvA = fopen('output/A_Q1.csv', 'w');
$fieldlist = array('', 'c', 'm', 'r', 'v', 'total');
fputcsv($csvA,$fieldlist);
foreach($content->A as $k=>$v){
    $row = array($k,$v->c,$v->m,$v->r,$v->v,$v->total);
    fputcsv($csvA,$row);
}
fclose($csvA);

$csvB = fopen('output/B_Q1.csv', 'w');
$fieldlist = array('', 'c', 'm', 'r', 'v', 'total');
fputcsv($csvB,$fieldlist);
foreach($content->B as $k=>$v){
    $row = array($k,$v->c,$v->m,$v->r,$v->v,$v->total);
    fputcsv($csvB,$row);
}
fclose($csvB);