<?php
$host = 'localhost';
$dbname = 'apotik';
$username_db = 'root';
$password_db = 'tidaktau321';
// $password_db = '';

$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username_db,
    $password_db,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);
