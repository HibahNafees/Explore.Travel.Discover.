<?php

$host = "localhost";
$dbname = "register";
$username = "root";
$password = "";

$mysqli = new mysqli(
    hostname: $host,
    username: $username,
    password: $password,
    database: $dbname
);

if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->error);
}

return $mysqli;
