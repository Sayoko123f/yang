<?php
$jsonname = 'dataset.json';
$json = fopen($jsonname, 'r');
$dataset  = json_decode(fread($json, filesize($jsonname)));
fclose($json);

$A = new Wordset('A', $dataset);
$B = new Wordset('B',$dataset);

$outjsonA = fopen('Q2_A.json','w');
fwrite($outjsonA,json_encode($A));
fclose($outjsonA);
$outjsonB = fopen('Q2_B.json','w');
fwrite($outjsonB,json_encode($B));
fclose($outjsonB);

class Wordset
{
    public $total;
    public $overlapRate;

    public function __construct($ab, $dataset)
    {
        $this->overlapRate = array();
        $t = $ab === 'A' ? 'A' : 'B';
        foreach (range(99, 109) as $v) {
            $this->readJson($t, $v, $dataset);
        }
        $this->total();
        $this->cal_overlapRate();
    }

    private function readJson($ab, $year, $dataset)
    {
        $arr = array();
        $year = strval($year);
        foreach ($dataset->$ab->$year as $k => $v) {
            $arr[] = $k;
        }
        $arr = array_unique($arr);
        $this->$year = $arr;
    }

    private function total()
    {
        $arr = array();
        foreach (range(99, 109) as $v) {
            foreach ($this->$v as $vv) {
                $arr[] = $vv;
            };
        }
        $this->total = array_unique($arr);
    }

    private function cal_overlapRate()
    {
        $countTotal = count($this->total);
        foreach (range(99, 109) as $v) {
            $this->overlapRate[strval($v)] = count($this->$v) / $countTotal;
        }
    }
}
