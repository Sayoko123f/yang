<?php
$csv = fopen('maindata.csv', 'r');
$rownum = 0;
$years = range(99,109);

while ($line = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }
    print_r($line);
break;
}
fclose($csv);
