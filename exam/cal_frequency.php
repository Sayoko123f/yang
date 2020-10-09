<?php
$jsonname = 'dataset.json';
$json = fopen($jsonname, 'r');
$dataset  = json_decode(fread($json, filesize($jsonname)));
fclose($json);
$ab = true;
$outputname = $ab?'A_frequentcy.csv':'B_frequentcy.csv';
// cal
$arr = cal($dataset, $ab);
// output
$frequentcy_csv = fopen($outputname, 'w');
fputcsv($frequentcy_csv, ['word', 'frequentcy']);
foreach ($arr as $k => $v) {
    $tmp = array();
    $tmp[] = $k;
    $tmp[] = $v;
    fputcsv($frequentcy_csv, $tmp);
}


fclose($frequentcy_csv);

function cal($dataset, $ab = true)
{
    $t = $ab ? 'A' : 'B';
    $arr = array();
    foreach ($dataset->$t as $year => $v) {
        foreach ($v as $word => $count) {
            //echo $word . $count;
            if (!isset($arr[$word])) {
                $arr[$word] = intval($count);
            } else {
                $arr[$word] += intval($count);
            }
            //break;
        }
    }
    return $arr;
}
