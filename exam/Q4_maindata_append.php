<?php
$csv = fopen('maindata.csv', 'r');
$rownum = 0;

$pool = array();

while ($line = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }
    // if ($rownum > 10) {
    //     break;
    // }

    $pool[] = new Row($line);
}
fclose($csv);
$csv = fopen('maindata.csv', 'r');
$rownum = 0;
while ($line = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }
    // if ($rownum > 10) {
    //     break;
    // }
    $key = $line[4] . "($line[7])";
    // echo '$key:' . "$key" . PHP_EOL;
    foreach ($pool as &$v) {
        // echo '$v->getkey():' . $v->getkey() . PHP_EOL;
        if ($v->getKey() === $key) {
            $v->append($line);
        }
    }
    unset($v);
}
fclose($csv);

//output
$outputcsv = fopen('./Q4.csv', 'w');
$fieldlist = array('ab', 'year', 'questionType', 'title', 'word', 'words', 'count', 'level', 'pos', 'fromChoices','numOfYears','srcQuestions','srcChoices');
fputcsv($outputcsv, $fieldlist);
foreach($pool as $v){
    fputcsv($outputcsv,$v->toArray());
}
fclose($outputcsv);

class Row
{
    public $ab;
    public $year;
    public $questionType;
    public $title;
    public $word;
    public $words;
    public $count;
    public $level;
    public $pos;
    public $fromChoices;
    public $NumOfYears = 0;
    public $source_questions = 0;
    public $source_choices = 0;
    private $key = "";
    private $NumOfYearsPool = array();

    public function __construct($line)
    {
        $this->ab = $line[0];
        $this->year = $line[1];
        $this->questionType = $line[2];
        $this->title = $line[3];
        $this->word = $line[4];
        $this->words = $line[5];
        $this->count = $line[6];
        $this->level = $line[7];
        $this->pos = $line[8];
        $this->fromChoices = $line[9];
        $this->setKey();
    }

    public function setKey()
    {
        $this->key = $this->word . "($this->level)";
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function append($line)
    {
        $this->NumOfYearsPool[] = $line[1];
        $this->NumOfYearsPool = array_unique($this->NumOfYearsPool);
        $this->NumOfYears = count($this->NumOfYearsPool);

        $src = $line[9] === '0' ? 'source_questions' : 'source_choices';
        $this->$src += $line[6];
    }

    public function toArray(){
        $arr = array('ab'=>$this->ab,
        'year'=>$this->year,
        'questionType'=>$this->questionType,
        'title'=>$this->title,
        'word'=>$this->word,
        'words'=>$this->words,
        'count'=>$this->count,
        'level'=>$this->level,
        'pos'=>$this->pos,
        'fromChoices'=>$this->fromChoices,
        'numOfYears'=>$this->NumOfYears,
        'srcQuestions'=>$this->source_questions,
        'srcChoices'=>$this->source_choices);
        return $arr;
    }
}
