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
    die("Connect Error: " . $e->getMessage());
}