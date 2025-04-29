<?php
session_start();
require 'vendor/autoload.php';
require 'db.php';

if ($_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Admins only']);
    exit;
}

header('Content-Type: application/json');

$sql = "
SELECT 
    er.id,
    e.title           AS event_title,
    er.registered_at,
    COALESCE(u.username, er.guest_name) AS registrant_name,
    er.guest_email                    AS registrant_email
FROM event_registrations er
LEFT JOIN users u    ON er.user_id  = u.user_id
LEFT JOIN events e   ON er.event_id = e.event_id
ORDER BY er.registered_at DESC
";
$stmt = $pdo->query($sql);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($registrations);
