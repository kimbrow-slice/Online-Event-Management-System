<?php

require_once 'db.php';
session_start();
if (!in_array($_SESSION['user_type'], ['admin', 'organizer'])) {
  http_response_code(403);
  exit('Unauthorized');
}

header('Content-Type: text/html; charset=UTF-8');

$stmt = $pdo->query("
  SELECT event_id, event_date, event_time, location, description
  FROM events
  WHERE status = 'open'
  ORDER BY event_date, event_time
");

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id   = htmlspecialchars($r['event_id'],   ENT_QUOTES);
    $date = htmlspecialchars($r['event_date'], ENT_QUOTES);
    $time = htmlspecialchars($r['event_time'], ENT_QUOTES);
    $loc  = htmlspecialchars($r['location'],   ENT_QUOTES);
    $desc = htmlspecialchars($r['description'],ENT_QUOTES);

    echo "
      <tr>
        <td>{$date}</td>
        <td>{$time}</td>
        <td>{$loc}</td>
        <td>{$desc}</td>
        <td>
          <button
            class=\"btn btn-primary btn-sm attend-btn\"
            data-id=\"{$id}\">
            Attend
          </button>
        </td>
      </tr>
    ";
}
?>
