<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

session_start();
header('Content-Type: application/json');
require 'db.php';

// Make sure ONLY admins access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo "Access Denied.";
    exit;
}

$stmt = $pdo->query("
    SELECT 
      event_id,
      title,
      event_date,
      event_time,
      location,
      description
    FROM events
    ORDER BY event_date, event_time
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = htmlspecialchars($row['event_id'], ENT_QUOTES);
    $title = htmlspecialchars($row['title'], ENT_QUOTES);
    $date = htmlspecialchars($row['event_date'], ENT_QUOTES);
    $time = htmlspecialchars($row['event_time'], ENT_QUOTES);
    $loc = htmlspecialchars($row['location'] ?? 'TBD', ENT_QUOTES);
    $desc = htmlspecialchars($row['description'], ENT_QUOTES);

    echo "
      <tr>
        <td>{$id}</td>
        <td>{$title}</td>
        <td>{$date}</td>
        <td>{$time}</td>
        <td>{$loc}</td>
        <td>{$desc}</td>
        <td>
         <button class=\"btn btn-sm btn-info edit-event-btn\" data-id=\"{$id}\">Edit</button>
         <button class=\"btn btn-sm btn-danger delete-event-btn\" data-id=\"{$id}\">Delete</button>
        </td>
      </tr>
    ";
}
?>