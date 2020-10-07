<?php
require_once('$con.php');
$dir = "./maindatav2/";
$files = glob($dir . '*.txt');
foreach ($files as $filename) {
    $txt = fopen($filename, 'r');
    if (!preg_match('/(A|B)-(\d{1,3})-([A-Z])\s?-\s?Q\d{1,3}to\d{1,3}/', $filename)) {
        die("$filename filename title error :(");
    }
    $title = str_replace('.txt', '', $filename);
    $title = str_replace(' ', '', $title);
    $title = str_replace('./maindatav2/', '', $title);
    $content = fread($txt, filesize($filename));
    fclose($txt);
    if (!$content) {
        die("$filename fread error :(");
    }
    try {
        $sql = "INSERT INTO `exam_question`(`title`,`content`) VALUES (:title,:content);";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->execute();
    } catch (\PDOException $e) {
        die($filename.' : '.$e->getMessage());
    }
}
