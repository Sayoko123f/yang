<?php
try {
    $host = "localhost";
    $db_name = "shinai";
    $db_user = "Shinai";
    $db_password = "123";
    $con = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_password); //(server_name,user,password)
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("連接資料庫失敗: " . $e->getMessage() . "<br/>");
}

$sql = 'CREATE TABLE IF NOT EXISTS `shinai`.`kmdn` ( `id` INT NOT NULL AUTO_INCREMENT , `url` TEXT NOT NULL , `heading` TEXT NOT NULL , `date` TEXT NOT NULL , `author` TEXT NOT NULL , `clickRate` INT UNSIGNED NOT NULL , `content` TEXT NULL , PRIMARY KEY (`id`), UNIQUE (`url`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;';
$con->query($sql);

/*
CREATE TABLE `shinai`.`kmdn` ( `id` INT NOT NULL AUTO_INCREMENT , `url` TEXT NOT NULL , `heading` TEXT NOT NULL , `date` TEXT NOT NULL , `author` TEXT NOT NULL , `clickRate` INT UNSIGNED NOT NULL , `content` TEXT NULL , PRIMARY KEY (`id`), UNIQUE (`url`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
*/