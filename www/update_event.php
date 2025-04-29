<?php
require_once 'db.php';
session_start();
if ($_SESSION['user_type']!=='admin') { http_response_code(403); exit; }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int) ($_POST['event_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $event_time = trim($_POST['event_time'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$event_id || !$title || !$event_date || !$event_time || !$location || !$description) {
        http_response_code(400);
        echo "Missing fields.";
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE events
        SET title = ?, event_date = ?, event_time = ?, location = ?, description = ?
        WHERE event_id = ?
    ");
    if ($stmt->execute([$title, $event_date, $event_time, $location, $description, $event_id])) {
        echo "Event updated successfully.";
    } else {
        http_response_code(500);
        echo "Failed to update.";
    }
}
?>
