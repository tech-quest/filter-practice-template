<?php
function connect(): PDO
{
    $dsn = 'mysql:host=mysql; dbname=tq_filter; charset=utf8';
    $dbUserName = 'root';
    $dbPassword = 'password';
    $pdo = new PDO($dsn, $dbUserName, $dbPassword);

    return $pdo;
}
?>
