<?php
$csv = fopen('maindata.csv', 'r');
$rownum = 0;

$arr = array();
$data = new Data();

while ($line = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }
    //var_dump($line);
    echo $rownum . PHP_EOL;
    switch ($line[0]) {
        case 'A':
            $word = $line[4]."($line[7])";
            $data->A[$line[1]]->pushWord($line[2], $word);
        break;
        case 'B':
            $word = $line[4]."($line[7])";
            $data->B[$line[1]]->pushWord($line[2], $word);
            break;

        default:
            echo PHP_EOL . $rownum . PHP_EOL;
            die('Unknown questionType!!!');
    }
}
$data = $data->getData();
print_r($data);
$json = fopen('./unique_maindata.json', 'w');
fwrite($json, json_encode($data));
fclose($json);
fclose($csv);

class Data
{
    public $A;
    public $B;
    public function __construct()
    {
        $this->A = array();
        $this->B = array();
        foreach (range(99, 109) as $year) {
            $year = strval($year);
            $this->A[$year] = new Year('A');
            $this->B[$year] = new Year('B');
        }
    }

    public function getData()
    {
        foreach ($this->A as $v) {
            $v->getCount();
        }
        foreach ($this->B as $v) {
            $v->getCount();
        }
        return $this;
    }
}

class Year
{
    public $c = 0;
    public $m = 0;
    public $r = 0;
    public $v = 0;
    public $d = 0;
    public $total = 0;
    private $ab;
    private $cw = array(), $mw = array(), $rw = array(), $vw = array(), $dw = array();

    public function __construct($ab)
    {
        if ($ab === 'A') {
            $this->ab = 'A';
            unset($this->d);
            unset($this->dw);
        } else {
            $this->ab = 'B';
        }
    }

    public function pushWord($type, $word)
    {
        switch ($type) {
            case 'C':
                $this->cw[] = $word;
                break;
            case 'M':
                $this->mw[] = $word;
                break;
            case 'R':
                $this->rw[] = $word;
                break;
            case 'V':
                $this->vw[] = $word;
                break;
            case 'D':
                if ($this->ab === 'A') {
                    die("Error type pushWord() $type");
                }
                $this->dw[] = $word;
                break;
            default:
                die("Error type pushWord() $type");
        }
    }

    private function setWordUniqueCount()
    {
        $this->c = count(array_unique($this->cw));
        $this->m = count(array_unique($this->mw));
        $this->r = count(array_unique($this->rw));
        $this->v = count(array_unique($this->vw));
        if ($this->ab === 'B') {
            $this->d = count(array_unique($this->dw));
            $this->total += $this->d;
        }
        $this->total += $this->c + $this->m + $this->r + $this->v;
    }

    public function getCount(): array
    {
        $arr = array();
        $this->setWordUniqueCount();
        $arr['C'] = $this->c;
        $arr['M'] = $this->m;
        $arr['R'] = $this->r;
        $arr['V'] = $this->v;
        if ($this->ab === 'b') {
            $arr['D'] = $this->d;
        }
        $arr['Total'] = $this->total;
        return $arr;
    }
}
