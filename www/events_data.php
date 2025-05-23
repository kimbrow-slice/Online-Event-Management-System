<?php
require_once __DIR__ . '/db.php';  


$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    // Filtered query
    $sql = "
      SELECT
        event_id,
        title,
        event_date,
        event_time,
        location,
        description
      FROM events
      WHERE status = 'open'
        AND (
             title       LIKE :t
          OR description LIKE :d
        )
      ORDER BY event_date, event_time
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      't' => "%{$search}%",
      'd' => "%{$search}%"
    ]);
} else {
    // No search: fetch all open events
    $stmt = $pdo->query("
      SELECT
        event_id,
        title,
        event_date,
        event_time,
        location,
        description
      FROM events
      WHERE status = 'open'
      ORDER BY event_date, event_time
    ");
}
/*
$stmt = $pdo->query("
    SELECT 
      event_id,
      title,
      event_date,
      event_time,
      location,
      description
    FROM events
    WHERE status = 'open'
    ORDER BY event_date, event_time
");
*/
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id    = htmlspecialchars($row['event_id'],   ENT_QUOTES);
    $title = htmlspecialchars($row['title'],      ENT_QUOTES);
    $date  = htmlspecialchars($row['event_date'], ENT_QUOTES);
    $time  = htmlspecialchars($row['event_time'], ENT_QUOTES);
    $loc   = htmlspecialchars($row['location'] ?? 'TBD', ENT_QUOTES);
    $desc  = htmlspecialchars($row['description'], ENT_QUOTES);

    echo "
      <tr>
        <td>{$title}</td>
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
