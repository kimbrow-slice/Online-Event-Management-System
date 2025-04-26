<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$eventId = $_POST['event_id'] ?? null;
$guestEmail = $_POST['guest_email'] ?? '';

if (!$eventId || !is_numeric($eventId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid event ID']);
    exit;
}

if (!$guestEmail || !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO registrations (event_id, guest_email)
        VALUES (:event_id, :guest_email)
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'guest_email' => $guestEmail
    ]);
    echo json_encode(['message' => 'You’ve been registered successfully!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
}
?>