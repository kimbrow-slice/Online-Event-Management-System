<?php

require_once 'db.php';

$stmt = $pdo->query("
  SELECT event_id, title, event_date, status
  FROM events
  ORDER BY event_date
");

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
      <td>" . htmlspecialchars($r['title'])      . "</td>
      <td>" . htmlspecialchars($r['event_date']) . "</td>
      <td>" . htmlspecialchars($r['status'])     . "</td>
      <td>
        <button class='update-status-btn' 
                data-id='{$r['event_id']}' 
                data-status='{$r['status']}'>Change Status</button>
        <button class='delete-event-btn' 
                data-id='{$r['event_id']}'>Delete</button>
      </td>
    </tr>";
}
?>