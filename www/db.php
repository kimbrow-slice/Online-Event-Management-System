<?php

$DB_HOST = 'localhost';
$DB_NAME = 'event_portal';
$DB_USER = 'root';       
$DB_PASS = '';           

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('DB connection failed: '.$e->getMessage());
}