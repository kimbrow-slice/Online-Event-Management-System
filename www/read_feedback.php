<?php
// Admin view of all feedback entries
session_start();
require_once __DIR__ . '/db.php';

// Fetch feedback joined with registration info (including guest_name)
$stmt = $pdo->query("
    SELECT
        f.feedback_id,
        f.rating,
        f.comments,
        r.registration_id,
        r.user_id,
        r.guest_name,
        r.guest_email
    FROM feedback f
    JOIN registrations r
      ON f.registration_id = r.registration_id
    ORDER BY f.submitted_at DESC
");

if (! $stmt) {
    echo "Error loading feedback.";
    exit;
}

// Render as a bootstrap‐style table
echo '<table class="table table-striped">';
echo '  <thead>';
echo '    <tr>';
echo '      <th>Feedback #</th>';
echo '      <th>Registration #</th>';
echo '      <th>By</th>';
echo '      <th>Rating</th>';
echo '      <th>Comments</th>';
echo '    </tr>';
echo '  </thead>';
echo '  <tbody>';

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Determine whether it was a registered user or a guest
    if ($row['user_id']) {
        $who = 'User #' . htmlspecialchars($row['user_id']);
    } else {
        $who = htmlspecialchars($row['guest_name'] ?: $row['guest_email']);
    }

    echo '<tr>';
    echo '  <td>' . htmlspecialchars($row['feedback_id'])    . '</td>';
    echo '  <td>' . htmlspecialchars($row['registration_id']). '</td>';
    echo '  <td>' . $who                                     . '</td>';
    echo '  <td>' . htmlspecialchars($row['rating'])         . '</td>';
    echo '  <td>' . htmlspecialchars($row['comments'])       . '</td>';
    echo '</tr>';
}

echo '  </tbody>';
echo '</table>';
?>
