<?php
$csv = fopen('maindata.csv', 'r');
$rownum = 0;

/**
 * $line[0]=='ab'
 * $line[1]=='year'
 * $line[2]=='questionType' array('A'=>array('C','M','R','V'),'B'=>array('C','M','R','V','D'))
 * $line[3]=='title'
 * $line[4]=='word'
 * $line[5]=='words'
 * $line[6]=='count'
 * $line[7]=='level'
 * $line[8]=='pos'
 * $line[9]=='fromChoices'
 */
//init
$dataset = new Dataset();

while ($line = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }
    //var_dump($line);
    //var_dump(parseLine($line));
    $dataset->pushData($line[1], $line);
}
var_dump($dataset->getData());
$json = fopen('./dataset.json', 'w');
fwrite($json, json_encode($dataset));
fclose($json);
fclose($csv);




class Dataset
{
    public $years;
    public $A;
    public $B;

    public function __construct()
    {
        $this->years = range(99, 109);
        $this->A = new \stdClass();
        $this->B = new \stdClass();
        foreach ($this->years as $year) {
            $this->A->$year = array();
            $this->B->$year = array();
        }
    }

    public function getData($ab = 'A')
    {
        return $this->$ab;
    }

    public function pushData($year, $line)
    {

        $arr = self::parseLine($line);
        if ($line[0] === 'A') {
            if (!isset($this->A->$year[$arr[0]])) {
                $this->A->$year[$arr[0]] = $arr[1];
            } else {
                $this->A->$year[$arr[0]] += $arr[1];
            }
        } else if ($line[0] === 'B') {
            if (!isset($this->B->$year[$arr[0]])) {
                $this->B->$year[$arr[0]] = $arr[1];
            } else {
                $this->B->$year[$arr[0]] += $arr[1];
            }
        }
    }

    protected static function parseLine($line)
    {
        $arr = array();
        $key = $line[4] . '(' . $line[7] . ')';
        $value = intval($line[6]);
        $arr[] = $key;
        $arr[] = $value;
        return $arr;
    }
}
