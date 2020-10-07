<?php
require_once('$con.php');
$dir = "./output/";
$files = glob($dir . '*.txt');
$i = 0;
foreach ($files as $filename) {
    if (++$i > 20) {
        break;
    }
    echo PHP_EOL . 'filename: ' . $filename . PHP_EOL;
    $jsonfilename = str_replace('.txt', '_words.json', $filename);

    // JSON
    $f = fopen($jsonfilename, 'r');
    $line = fgets($f);
    $line = filter_symbol($line);
    $arr = json_decode($line);
    $questionNumber = '';
    foreach ($arr as $k => $v) {
        $item = new Item($jsonfilename, $k, $v);
        echo $item->word . ' ';
        try {
            $sql = "INSERT INTO `exam_question_words`(`ab`,`year`,`questionNumber`,`questionType`,`word`,`words`,`count`,`level`) VALUES(:ab,:year,:questionNumber,:questionType,:word,:words,:count,:level);";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':ab', $item->ab);
            $stmt->bindParam(':year', $item->year);
            $stmt->bindParam(':questionNumber', $item->questionNumber);
            $stmt->bindParam(':questionType', $item->questionType);
            $stmt->bindParam(':word', $item->word);
            $stmt->bindParam(':words', $item->words);
            $stmt->bindParam(':count', $item->count);
            $stmt->bindParam(':level', $item->level);
            $stmt->execute();
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
        $questionNumber = $item->questionNumber;
        //echo ':'.$questionNumber.PHP_EOL;
    }
    fclose($f);
    // endJSON
    // txt
    $t = fopen($filename, 'r');
    $line = fgets($t);
    $line = filter_symbol($line, false);
    //echo PHP_EOL . $line . PHP_EOL;
    try {
        $sql = "INSERT INTO `exam_question`(`questionNumber`,`content`) VALUES(:questionNumber,:content);";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':questionNumber', $questionNumber);
        $stmt->bindParam(':content', $line);
        $stmt->execute();
    } catch (\PDOException $e) {
        die($e->getMessage());
    }
    fclose($t);
}

class Item
{
    public $ab; //"A","B"
    public $year; //98,99,100,101
    public $questionNumber; //"$ab$yearQ41to44","$ab$yearQ45to48"
    public $questionType; // "R","M"
    public $word;
    public $words;
    public $count;
    public $level;

    /**
     * $name = $filename
     * $word = $k
     * $obj = $v
     */
    public function __construct($name, $word, $obj)
    {
        $this->setName($name);
        $this->word = self::myfilter($word);
        $this->words = self::myfilter($obj->words);
        $this->count = $obj->count;
        $this->level = $obj->level;
    }

    protected function setName($name)
    {
        if (!preg_match('/(A|B)\s?-(\d{1,3})-\s?(Q\w+?)-\s?([A-Z])/', $name, $matches)) {
            die($name . 'setName Error!');
        };
        $this->ab = $matches[1];
        $this->year = intval($matches[2]);
        $this->questionType = $matches[4];
        $this->questionNumber = $this->ab . $this->year . $matches[3] . $this->questionType;
    }

    protected static function myfilter($str)
    {
        return str_replace('’', '\'', $str);
    }
}

//’
//(A|B)-(\d{1,3})-\s?(Q\w+?)-\s?([A-Z])
function filter_symbol($str, $backslash = true)
{
    $tmp = $backslash ? '\\' : '';
    $str = str_replace('\'', '"', $str);
    $str = str_replace('“', $tmp . '"', $str);
    $str = str_replace('”', $tmp . '"', $str);
    return $str;
}
