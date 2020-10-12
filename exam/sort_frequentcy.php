<?php
$ab = true;
$filename = $ab ? 'A_frequentcy.csv' : 'B_frequentcy.csv';
$outputfilename = $ab ? 'A_sort_frequentcy.csv' : 'B_sort_frequentcy.csv';
$inputfile = fopen($filename, 'r');

$bufferArr = array();
while ($line = fgetcsv($inputfile)) {
    $bufferArr[] = new Line($line);
}
usort($bufferArr, function ($a, $b) {
    return $a->count < $b->count;
});
print_r($bufferArr);

//output
$outputfile = fopen($outputfilename, 'w');
foreach ($bufferArr as $v) {
    $line = array();
    $line[] = $v->word;
    $line[] = $v->count;
    fputcsv($outputfile, $line);
}
fclose($outputfile);



class Line
{

    public $word;
    public $count;

    public function __construct($line)
    {
        $this->word = $line[0];
        $this->count = intval($line[1]);
    }
}
