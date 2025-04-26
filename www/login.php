<?php
session_start();
require_once 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT user_id, pass_hash FROM users WHERE username = ?');
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && password_verify($password, $row['pass_hash'])) {
    $_SESSION['user_id'] = $row['user_id'];  // Use $row instead of $user
    $_SESSION['user_type'] = $row['user_type'];  // Use $row instead of $user
    header('Location: index.html');
    exit;
}

header('Location: login.html?err=Invalid%20credentials');
