<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $status      = trim($_POST['status'] ?? 'upcoming');

    // TODO: Add server-side validation (e.g. non-empty title, valid datetime)

    $stmt = $pdo->prepare("
        INSERT INTO events (title, description, event_date, status)
        VALUES (?, ?, ?, ?)
    ");
    if ($stmt->execute([$title, $description, $event_date, $status])) {
        echo "Created event with ID: " . $pdo->lastInsertId();
    } else {
        echo "Error: " . implode(', ', $stmt->errorInfo());
    }
}
?>
