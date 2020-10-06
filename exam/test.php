<?php
$f = fopen('./maindata/B-109-R-Q44to47_words.json','r');
    $line = fgets($f);
    $line = filter_symbol($line);
    //echo $line.PHP_EOL;
    $obj = json_decode($line);
    echo $line;
    var_dump($obj);
    
    
    function filter_symbol($str, $backslash = true)
{
    $tmp = $backslash ? '\\' : '';
    $str = str_replace('\'', '"', $str);
    $str = str_replace('“', $tmp . '"', $str);
    $str = str_replace('”', $tmp . '"', $str);
    return $str;
}
