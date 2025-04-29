<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int) ($_POST['event_id'] ?? 0);

    if (!$event_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing event ID']);
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $guest_name = trim($_POST['guest_name'] ?? '');
    $guest_email = trim($_POST['guest_email'] ?? '');

    if (!$user_id && (!$guest_name || !$guest_email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing guest details']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO event_registrations (event_id, user_id, guest_name, guest_email, registered_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $success = $stmt->execute([
        $event_id,
        $user_id,
        $guest_name,
        $guest_email
    ]);

    if ($success) {
        echo json_encode(['message' => 'Registration successful']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed']);
    }
}
?>
