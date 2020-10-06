<?php
require_once('$con.php');
$dir = "./maindatav2/";
$files = glob($dir . '*.txt');
$csv = fopen('maindata.csv', 'w');
$fieldlist = array('ab', 'year', 'questionType', 'title', 'word', 'words', 'count', 'level', 'pos');
fwrite($csv, implode(',', $fieldlist));
fwrite($csv, PHP_EOL);
$i = 0;

foreach ($files as $filename) {
    /*if (++$i > 2) {
        break;
    }*/
    //echo $filename . PHP_EOL;
    $jsonfilename = str_replace('.txt', '_words.json', $filename);
    //echo 'start read ' . $jsonfilename . PHP_EOL;
    //start read JSON
    $f = fopen($jsonfilename, 'r');
    $line = fgets($f);
    $line = filter_symbol($line);
    //echo $line.PHP_EOL;
    $obj = json_decode($line);
    if (!$obj) {
        echo $jsonfilename . ' json_decode error:(' . PHP_EOL;
        fclose($f);
        fclose($csv);
        continue;
    }
    //var_dump($obj);
    //echo $obj->title . PHP_EOL;

    if (!preg_match('/(A|B)-(\d{1,3})-([A-Z])\s?-\s?Q\d{1,3}to\d{1,3}/', $obj->title, $matches)) {
        fclose($f);
        fclose($csv);
        die($jsonfilename . 'titleParse Error:(');
        /*echo $jsonfilename . 'titleParse Error:(' . PHP_EOL;
        continue;*/
    };


    //have choices question type:
    // A-M, A-R, A-V
    // B-M, B-R, B-V
    if ($matches[3] === 'M' || $matches[3] === 'R' || $matches[3] === 'V') {
        if (!isset($obj->choices) || $obj->choices === '' || !$obj->choices) {
            fclose($f);
            fclose($csv);
            die($jsonfilename . 'titleParse Error:(');
            /*echo $obj->title . 'choices error' . PHP_EOL;
            continue;*/
        }
        foreach ($obj->choices as $k => $v) {
            /*
        echo $k . PHP_EOL //string
            . $v->words . PHP_EOL //string
            . $v->count . PHP_EOL //int
            . $v->level . PHP_EOL //int
            . $v->pos . PHP_EOL; //string
            */
            //echo "$k, ";
        }
        createItem($matches, $k, $v, $csv);
    }

    //passage

    //print_r($obj->passage);
    foreach ($obj->passage as $k => $v) {
        /*
        echo $k . PHP_EOL //string
            . $v->words . PHP_EOL //string
            . $v->count . PHP_EOL //int
            . $v->level . PHP_EOL //int
            . $v->pos . PHP_EOL; //string
            */
        //echo "$k, ";
        createItem($matches, $k, $v, $csv);
    }

    fclose($f);
}
fclose($csv);

function createItem($matches, $k, $v, $handle)
{
    $item = new Item($matches, $k, $v);
    //var_dump($item);
    fwrite($handle, $item);
}



class Item
{
    public $ab;
    public $year; // int
    public $questionType;
    public $title;
    public $word;
    public $words;
    public $count; // int
    public $level; // int
    public $pos;

    public function __construct($titlematches, $k, $v)
    {
        $this->titleParse($titlematches);
        $this->word = $k;
        $this->words = $v->words;
        $this->count = $v->count;
        $this->level = $v->level;
        $this->pos = $v->pos;
    }


    protected function titleParse($matches)
    {

        //var_dump($matches);
        $this->title = $matches[0];
        $this->ab = $matches[1];
        $this->year = intval($matches[2]);
        $this->questionType = $matches[3];
        return true;
    }

    protected static function myfilter($str)
    {
        return str_replace('’', '\'', $str);
    }

    function __toString()
    {
        return $this->ab . ','
            . $this->year . ','
            . $this->questionType . ','
            . $this->title . ','
            . $this->word . ','
            . $this->words . ','
            . $this->count . ','
            . $this->level . ','
            . $this->pos
            . PHP_EOL;
    }
}

//’
function filter_symbol($str, $backslash = true)
{
    $tmp = $backslash ? '\\' : '';
    $str = str_replace('\'', '"', $str);
    $str = str_replace('“', $tmp . '"', $str);
    $str = str_replace('”', $tmp . '"', $str);
    return $str;
}
