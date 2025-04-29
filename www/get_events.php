<?php
require 'vendor/autoload.php';
require 'db.php';

header('Content-Type: application/json');

$eventId = $_GET['event_id'] ?? null;

if (!$eventId || !is_numeric($eventId)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid event ID']);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
  http_response_code(404);
  echo json_encode(['error' => 'Event not found']);
  exit;
}

echo json_encode($event);
?>