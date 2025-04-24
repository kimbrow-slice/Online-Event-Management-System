<?php

require_once 'db.php'; 

$stmt = $pdo->query("
    SELECT event_id, title, event_date, status
    FROM events
    WHERE status IN ('upcoming','open')
    ORDER BY event_date ASC
");

if ($stmt) {
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>"
           . htmlspecialchars($row['title'])
           . " &mdash; " . htmlspecialchars($row['event_date'])
           . " [". htmlspecialchars($row['status']) ."]"
           . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error fetching events.";
}
?>
