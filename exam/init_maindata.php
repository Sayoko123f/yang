<?php
//require_once('$con.php');
$dir = "./maindatav3/";
$files = glob($dir . '*.txt');
$csv = fopen('maindata.csv', 'w');
$fieldlist = array('ab', 'year', 'questionType', 'title', 'word', 'words', 'count', 'level', 'pos', 'fromChoices');
fputcsv($csv, $fieldlist);
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
        continue;
    }
    //var_dump($obj);
    //echo $obj->title . PHP_EOL;

    if (!preg_match('/(A|B)-(\d{1,3})-([A-Za-z])\s?-\s?Q\d{1,3}to\d{1,3}/', $obj->title, $matches)) {
        fclose($f);
        fclose($csv);
        die($jsonfilename . ' titleParse Error:(');
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
            die($jsonfilename . ' titleParse Error:(');
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
        createItem($matches, $k, $v, $csv, true);
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
        createItem($matches, $k, $v, $csv, false);
    }

    fclose($f);
}
fclose($csv);

function createItem($matches, $k, $v, $handle, $fromChoices)
{
    if (!filiter_word($k)) {
        echo "filiter word: $k" . PHP_EOL;
        return false;
    }
    $item = new Item($matches, $k, $v, $fromChoices);
    //var_dump($item);
    fputcsv($handle, $item->tocsv());
    return true;
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
    public $fromChoices; // 

    public function __construct($titlematches, $k, $v, $fromChoices)
    {
        $this->titleParse($titlematches);
        $this->word = self::myfilter($k);
        $this->words = self::myfilter($v->words);
        $this->count = $v->count;
        $this->level = $v->level;
        $this->pos = $v->pos;
        $this->fromChoices = $fromChoices ? 1 : 0;
    }


    protected function titleParse($matches)
    {

        //var_dump($matches);
        $this->title = $matches[0];
        $this->title = str_replace('-099','-99',$this->title);
        $this->title = str_replace(array('c','m','r','v','d',),strtoupper($matches[3]),$this->title);
        $this->title = str_replace(' ','',$this->title);
        $this->ab = $matches[1];
        $this->year = intval($matches[2]);
        $this->questionType = strtoupper($matches[3]);
        return true;
    }

    protected static function myfilter($str)
    {
        return str_replace('’', '\'', $str);
    }



    function tocsv()
    {
        $arr = array(
            $this->ab,
            $this->year,
            $this->questionType,
            $this->title,
            $this->word,
            $this->words,
            $this->count,
            $this->level,
            $this->pos,
            $this->fromChoices
        );
        return $arr;
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

function str_to_csv($str)
{
    if (!is_int($str)) {
        $str = str_replace('"', '""', $str);
    }
    $str = '"' . $str . '"';
    return $str;
}

function filiter_word($str)
{
    if (preg_match('/^-/', $str)) {
        return false;
    }
    return true;
}
