<?php
try {
    $host = "localhost";
    $db_name = "cmx";
    $db_user = "cmx";
    $db_password = "ikmygb111";
    $con = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_password); //(server_name,user,password)
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("連接資料庫失敗: " . $e->getMessage() . "<br/>");
}
$con->query("DROP TABLE `test`;");
$sql = "CREATE TABLE IF NOT EXISTS `test`(
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
`word` JSON NOT NULL,
`category` JSON NOT NULL
)CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
$con->query($sql);
$dir = "./";

$glob = glob('*.txt');
foreach ($glob as $filename) {
    $handle = fopen($dir . $filename, "r");
    if (!$handle) {
        die("error: Empty file, $dir.$filename");
    }

    $data = array();
    while ($line = fgets($handle)) {
        $arr = myMatch($line);
        $data[] = $arr;
    }
    $data = flatten($data);
    $arr1 = array();
    $arr2 = array();
    foreach ($data as $v) {
        $arr1[] = $v->word;
        $arr2[] = $v->category;
    }
    $valueWord = json_encode($arr1);
    $valueCategory = json_encode($arr2);


    /** sql */
    $sql = "INSERT INTO `test` (`word`,`category`) VALUES (:word,:category);";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':word', $valueWord);
    $stmt->bindParam(':category', $valueCategory);
    $stmt->execute();
}
/**
 * @param string
 * @return array key:value = word:category
 */
function myMatch($str)
{
    $response = array();
    preg_match_all('/(.+?)\((.+?)\)\s/', $str, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $word = new Word($matches[1][$i], $matches[2][$i]);
        $response[] = $word;
    }
    return $response;
}

function flatten(array $array)
{
    $return = array();
    array_walk_recursive($array, function ($a) use (&$return) {
        $return[] = $a;
    });
    return $return;
}

class Word
{
    public $word;
    public $category;

    public function __construct(string $word, string $category)
    {
        if (!$word || !$category) {
            return null;
        }
        $this->word = $word;
        $this->category = $category;
    }

    function __toString()
    {
        return $this->word . ' : ' . $this->category;
    }
}
