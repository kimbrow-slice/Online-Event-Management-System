<?php
// Admin view of all feedback entries

require_once 'db.php';

$stmt = $pdo->query("
    SELECT f.feedback_id, f.rating, f.comments,
           r.registration_id, r.user_id, r.guest_email
    FROM feedback f
    JOIN registrations r ON f.registration_id = r.registration_id
    ORDER BY f.submitted_at DESC
");

if ($stmt) {
    echo "<table border='1'>
            <tr>
              <th>Feedback #</th>
              <th>Registration #</th>
              <th>By</th>
              <th>Rating</th>
              <th>Comments</th>
            </tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $who = $row['user_id']
             ? "User #{$row['user_id']}"
             : "Guest ({$row['guest_email']})";
        echo "<tr>"
           . "<td>{$row['feedback_id']}</td>"
           . "<td>{$row['registration_id']}</td>"
           . "<td>{$who}</td>"
           . "<td>{$row['rating']}</td>"
           . "<td>" . htmlspecialchars($row['comments']) . "</td>"
           . "</tr>";
    }
    echo "</table>";
} else {
    echo "Error loading feedback.";
}
?>
