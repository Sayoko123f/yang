<?php
$csv = fopen('maindata.csv', 'r');
$rownum = 0;

$dataA = new Data();
$dataB = new Data();
while ($line = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }

$isPassage = $line[9] === '0';
$t = $line[1];
if ($line[0] === 'A') {
    $dataA->$t->add($isPassage, intval($line[7]), intval($line[6]));
} else {
    $dataB->$t->add($isPassage, intval($line[7]), intval($line[6]));
}
}
fclose($csv);
$outjsonA = fopen('Q3_A.json','w');
fwrite($outjsonA,json_encode($dataA));
fclose($outjsonA);
$outjsonB = fopen('Q3_B.json','w');
fwrite($outjsonB,json_encode($dataB));
fclose($outjsonB);

class Data
{
    public function __construct()
    {
        foreach (range(99, 109) as $v) {
            $v = strval($v);
            $this->$v = new Countlevel();
        }
    }
}

class Countlevel
{
    public $passage;
    public $choices;

    public function __construct()
    {
        $this->passage = $this->initObj();
        $this->choices = $this->initObj();
    }

    public function add($isPassage, $level, $count)
    {
        $t = $isPassage ? 'passage' : 'choices';
        $level = 'level' . $level;
        $this->$t->$level += $count;
    }

    private function initObj()
    {
        $obj = new \stdClass();
        $obj->level1 = 0;
        $obj->level2 = 0;
        $obj->level3 = 0;
        $obj->level4 = 0;
        $obj->level5 = 0;
        $obj->level6 = 0;
        $obj->level9 = 0;
        return $obj;
    }
}
