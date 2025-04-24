<?php

require_once 'db.php';  // provides $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int) ($_POST['event_id'] ?? 0);
    $status   = trim($_POST['status'] ?? '');

    // TODO: Validate $status is one of allowed ENUM values

    $stmt = $pdo->prepare("
        UPDATE events
        SET status = ?
        WHERE event_id = ?
    ");
    if ($stmt->execute([$status, $event_id])) {
        echo "Event #{$event_id} status set to '{$status}'.";
    } else {
        echo "Error: " . implode(', ', $stmt->errorInfo());
    }
}
?>
