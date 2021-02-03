<?php
require('data/kmdn.php'); //$kmdn
try {
    $host = "localhost";
    $db_name = "test";
    $db_user = "test";
    $db_password = "123";
    $testcon = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_password); //(server_name,user,password)
    // set the PDO error mode to exception
    $testcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("連接資料庫失敗: " . $e->getMessage() . "<br/>");
}

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

$stmt = $testcon->query("SELECT * FROM `test`;");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$field = array('id', 'url', 'heading', 'date', 'author', 'clickRate', 'content');

$table = 'new';
$testcon->query("ALTER TABLE `new` MODIFY COLUMN `id` INT auto_increment");
$sql = "CREATE TABLE IF NOT EXISTS `$table`(" .
    "`id` INT NOT NULL PRIMARY KEY," .
    "`url` TEXT NOT NULL UNIQUE," .
    "`heading` TEXT NOT NULL," .
    "`date` INT NOT NULL," .
    "`author` TEXT NOT NULL," .
    "`clickRate` INT NOT NULL," .
    "`content` TEXT NOT NULL" .
    ")CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
$testcon->query($sql);
// foreach ($kmdn as $item) {
//     $timestamp = strtotime($item['date']);
//     if (!$timestamp) {
//         die($item['url'] . ' strtotime error:(');
//     }
//     $sql = "INSERT INTO `$table`(`id`,`url`,`heading`,`date`,`author`,`clickRate`,`content`) VALUES(:id,:url,:heading,:date,:author,:clickRate,:content);";
//     try {
//         $stmt = $testcon->prepare($sql);
//         $stmt->bindParam(":$field[0]", $item[$field[0]]);
//         $stmt->bindParam(":$field[1]", $item[$field[1]]);
//         $stmt->bindParam(":$field[2]", $item[$field[2]]);
//         $stmt->bindParam(":date", $timestamp);
//         $stmt->bindParam(":$field[4]", $item[$field[4]]);
//         $stmt->bindParam(":$field[5]", $item[$field[5]]);
//         $stmt->bindParam(":$field[6]", $item[$field[6]]);
//         $stmt->execute();
//     } catch (\PDOException $e) {
//         var_dump($item);
//         $stmt->debugDumpParams();
//         die($e->getMessage());
//     }
// }

foreach ($result as $item) {
    $timestamp = strtotime($item['date']);
    if (!$timestamp) {
        die($item['url'] . ' strtotime error:(');
    }
    $sql = "INSERT INTO `$table`(`url`,`heading`,`date`,`author`,`clickRate`,`content`) VALUES(:url,:heading,:date,:author,:clickRate,:content);";
    try {
        $stmt = $testcon->prepare($sql);
        $stmt->bindParam(":$field[1]", $item[$field[1]]);
        $stmt->bindParam(":$field[2]", $item[$field[2]]);
        $stmt->bindParam(":date", $timestamp);
        $stmt->bindParam(":$field[4]", $item[$field[4]]);
        $stmt->bindParam(":$field[5]", $item[$field[5]]);
        $stmt->bindParam(":$field[6]", $item[$field[6]]);
        $stmt->execute();
    } catch (\PDOException $e) {
        var_dump($item);
        $stmt->debugDumpParams();
        die($e->getMessage());
    }
}
