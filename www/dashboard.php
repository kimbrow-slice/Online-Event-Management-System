<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
</head>
<body style="font-family:sans-serif;text-align:center;margin-top:4rem">
  <h1>You are logged in!</h1>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>
