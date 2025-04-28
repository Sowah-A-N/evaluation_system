<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'course_evaluation');
define('DB_USER', 'root');
define('DB_PASS', '');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";


try{

    $conn = new PDO($dsn, DB_USER, DB_PASS);

}
catch (PDOException $e) {

    echo "ERROR CONNECTING TO DATABASE  : " . $e -> getMessage();

}