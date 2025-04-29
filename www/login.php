<?php
session_start();
require_once 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare(
    'SELECT user_id, pass_hash, user_type 
       FROM users 
      WHERE username = ?'
);
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && password_verify($password, $row['pass_hash'])) {
    // set session
    $_SESSION['user_id']   = $row['user_id'];
    $_SESSION['user_type'] = $row['user_type'];

    // redirect based on role
    if ($row['user_type'] === 'admin') {
        header('Location: admin.html');
    } else {
        header('Location: index.html');
    }
    exit;
}

// on failure
header('Location: login.html?err=Invalid%20credentials');
exit;
?>