<?php
session_start();
require_once 'db.php';

// Only admins/organizers can create
if (!in_array($_SESSION['user_type'] ?? '', ['admin','organizer'])) {
  http_response_code(403);
  echo 'Unauthorized';
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize inputs
  $title       = trim($_POST['title'] ?? '');
  $date        = trim($_POST['event_date'] ?? '');
  $time        = trim($_POST['event_time'] ?? '');
  $location    = trim($_POST['location'] ?? '');
  $description = trim($_POST['description'] ?? '');

  // Basic validation
  if (!$title || !$date || !$time || !$location || !$description) {
    http_response_code(400);
    echo 'All fields are required';
    exit;
  }

  // Insert into events table
  $stmt = $pdo->prepare("
    INSERT INTO events
      (title, event_date, event_time, location, description, status)
    VALUES (?, ?, ?, ?, ?, 'open')
  ");
  if (!$stmt->execute([$title, $date, $time, $location, $description])) {
    http_response_code(500);
    echo 'Failed to create event';
    exit;
  }

  // Success
  echo 'Event created';
  exit;
}

// Fall back to listing 
header('Content-Type: text/html; charset=UTF-8');
$stmt = $pdo->query("
  SELECT event_id, title, event_date, event_time, location, description
    FROM events
   WHERE status = 'open'
ORDER BY event_date, event_time
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $id    = htmlspecialchars($r['event_id'],   ENT_QUOTES);
  $title = htmlspecialchars($r['title'],      ENT_QUOTES);
  $date  = htmlspecialchars($r['event_date'], ENT_QUOTES);
  $time  = htmlspecialchars($r['event_time'], ENT_QUOTES);
  $loc   = htmlspecialchars($r['location'],   ENT_QUOTES);
  $desc  = htmlspecialchars($r['description'],ENT_QUOTES);

  echo "
    <tr>
      <td>{$title}</td>
      <td>{$date}</td>
      <td>{$time}</td>
      <td>{$loc}</td>
      <td>{$desc}</td>
      <td>
        <button class=\"btn btn-sm btn-info edit-event-btn\"   data-id=\"{$id}\">Edit</button>
        <button class=\"btn btn-sm btn-danger delete-event-btn\" data-id=\"{$id}\">Delete</button>
      </td>
    </tr>
  ";
}
?>
