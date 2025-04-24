<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int) ($_POST['event_id'] ?? 0);

    $stmt = $pdo->prepare("
        DELETE FROM events
        WHERE event_id = ?
    ");
    if ($stmt->execute([$event_id])) {
        echo "Event #{$event_id} deleted.";
    } else {
        echo "Error: " . implode(', ', $stmt->errorInfo());
    }
}
?>
