<?php
$json = fopen('Q3_A.json', 'r');
$contentA = json_decode(fread($json, filesize('Q3_A.json')));
fclose($json);
$json = fopen('Q3_B.json', 'r');
$contentB = json_decode(fread($json, filesize('Q3_B.json')));
fclose($json);

$csv = fopen('output/A_Q3.csv', 'w');
$fieldlist = array('', 'level1', 'level2', 'level3', 'level4', 'level5', 'level6', 'level9');
fputcsv($csv, $fieldlist);
foreach($contentA as $k=>$v){
    $row = array($k."_passage",$v->passage->level1,$v->passage->level2,$v->passage->level3,$v->passage->level4,$v->passage->level5,$v->passage->level6,$v->passage->level9);
    fputcsv($csv,$row);
    $row = array($k."_choices",$v->choices->level1,$v->choices->level2,$v->choices->level3,$v->choices->level4,$v->choices->level5,$v->choices->level6,$v->choices->level9);
    fputcsv($csv,$row);
}
fclose($csv);

$csv = fopen('output/B_Q3.csv', 'w');
$fieldlist = array('', 'level1', 'level2', 'level3', 'level4', 'level5', 'level6', 'level9');
fputcsv($csv, $fieldlist);
foreach($contentB as $k=>$v){
    $row = array($k."_passage",$v->passage->level1,$v->passage->level2,$v->passage->level3,$v->passage->level4,$v->passage->level5,$v->passage->level6,$v->passage->level9);
    fputcsv($csv,$row);
    $row = array($k."_choices",$v->choices->level1,$v->choices->level2,$v->choices->level3,$v->choices->level4,$v->choices->level5,$v->choices->level6,$v->choices->level9);
    fputcsv($csv,$row);
}
fclose($csv);