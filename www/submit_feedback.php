<?php
// submit_feedback.php
session_start();
require_once __DIR__ . '/db.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error'=>'Method not allowed']);
    exit;
}
// Debug: see what actually arrived in POST
error_log('POST payload: ' . print_r($_POST, true));
// Grab & validate inputs
$event_id = filter_input(INPUT_POST, 'registration_id', FILTER_VALIDATE_INT);
if (!$event_id || !$rating) {
    http_response_code(400);
    exit('Missing registration_id or rating');
}
$stmt = $pdo->prepare("
  INSERT INTO feedback (registration_id, rating, comments)
  VALUES (:eid, :rate, :comm)
");
$stmt->execute([
  ':eid'   => $registration_id,
  ':rate'  => $rating,
  ':comm'  => $comments,
]);


// Return success
echo json_encode(['success'=>true]);