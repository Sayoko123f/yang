<?php
require_once('$con.php');

$handle = fopen("out.csv", "r");
if (!$handle) {
    die("error opening the file $filename");
}
while (($line = fgets($handle))) {
    $fields = explode(",", $line);
    //echo $fields[0] . PHP_EOL;
    $sql = "INSERT INTO `7000words_level`(`word`,`pos`,`level`) VALUES(:word,:pos,:level);";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':word', $fields[0]);
    $stmt->bindParam(':pos', $fields[1]);
    $stmt->bindParam(':level', $fields[2]);
    $stmt->execute();
}
$con = null;
fclose($handle);
