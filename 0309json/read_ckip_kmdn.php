<?php
require('$con.php');
$dir = './kmdn/';
$files = glob($dir . '*.json');
$i = 0;
foreach ($files as $filename) {
    $json = json_decode(file_get_contents($filename));
    if (!$json) {
        die("$filename: Failed to json_decode.");
    }
    $arr = makeDataToDB($json);
    // var_dump($arr);

    $sql = "INSERT INTO `ckip_all`(`src`,`heading`,`unixtime`,`author`,`word`,`category`)VALUES
    (:src,:heading,:unixtime,:author,:word,:category);";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':src', $arr['url']);
    $stmt->bindParam(':heading', $arr['heading']);
    $stmt->bindParam(':unixtime', $arr['date']);
    $stmt->bindParam(':author', $arr['author']);
    $stmt->bindParam(':word', $arr['word']);
    $stmt->bindParam(':category', $arr['category']);
    try {
        $stmt->execute();
    } catch (\PDOException $e) {
        $stmt->debugDumpParams();
        echo $e->getCode().PHP_EOL.$e->getMessage().PHP_EOL;
        die($filename . PHP_EOL);
    }
    if ($i++ > 4) {
        break;
    }
}

/**
 * @param JSON
 * @return array
 */
function makeDataToDB($json)
{
    $url = $heading = $author = "";
    $date = 0;
    if ($json->meta->url && $json->meta->url !== "null") {
        $url = $json->meta->url;
    }
    if ($json->meta->heading && $json->meta->heading !== "null") {
        $heading = $json->meta->heading;
    }
    if ($json->meta->date && $json->meta->date !== "null" && is_int($json->meta->date)) {
        $date = $json->meta->date;
    }
    if ($json->meta->author && $json->meta->author !== "null") {
        $author = $json->meta->author;
    }
    $ctx = myMatch($json->meta->content);
    return array('url' => $url, 'heading' => $heading, 'date' => $date, 'author' => $author, 'word' => json_encode($ctx['word']), 'category' => json_encode($ctx['category']));
}

/**
 * @param string
 * @return array ['word'=>[],'category'=>[]]
 */
function myMatch($str)
{
    $response = array('word' => array(), 'category' => array());
    preg_match_all('/(.+?)\((.+?)\)\s/', $str, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $response['word'][] = $matches[1][$i];
        $response['category'][] = $matches[2][$i];
    }
    return $response;
}
