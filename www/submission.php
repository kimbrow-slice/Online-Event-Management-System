<?php

require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = (int) ($_POST['registration_id'] ?? 0);
    $rating          = (int) ($_POST['rating'] ?? 0);
    $comments        = trim($_POST['comments'] ?? '');

    // TODO: Validate rating between 1 and 5

    $stmt = $pdo->prepare("
        INSERT INTO feedback (registration_id, rating, comments)
        VALUES (?, ?, ?)
    ");
    if ($stmt->execute([$registration_id, $rating, $comments])) {
        echo "Thanks for your feedback!";
    } else {
        echo "Error: " . implode(', ', $stmt->errorInfo());
    }
}
?>
