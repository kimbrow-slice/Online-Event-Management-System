<?php
session_start();
require_once 'db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    exit('Missing fields');
}

try {
    $check = $pdo->prepare('SELECT 1 FROM users WHERE username = ?');
    $check->execute([$username]);
    if ($check->fetch()) {
        http_response_code(409);
        exit('Username already in use');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('INSERT INTO users (username, pass_hash) VALUES (?, ?)');
    $insert->execute([$username, $hash]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    header('Location: index.html');
} catch (PDOException $e) {
    http_response_code(500);
    exit('Server error');
}
