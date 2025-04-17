<?php
session_start();
require_once 'db.php';

$u = trim($_POST['username'] ?? '');
$p = $_POST['password'] ?? '';

if (!$u || !$p) { http_response_code(400); exit('Missing fields'); }

try {
    // unique username?
    $exists = $pdo->prepare("SELECT 1 FROM users WHERE username = ?");
    $exists->execute([$u]);
    if ($exists->fetch()) { http_response_code(409); exit('Username in use'); }

    $hash = password_hash($p, PASSWORD_BCRYPT);
    $ins  = $pdo->prepare("INSERT INTO users (username, pass_hash) VALUES (?,?)");
    $ins->execute([$u, $hash]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    // Redirect user
    header('Location: dashboard.php');
} catch (PDOException $e) {
    http_response_code(500); exit('Server error');
}