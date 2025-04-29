<?php

//db connection test file

require 'db.php';
try {
  $pdo->query('SELECT 1');
  echo " DB connection successful: " . $host;
} catch (\PDOException $e) {
  echo " DB connection failed: " . $e->getMessage();
}
?>