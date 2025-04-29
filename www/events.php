<?php
// events.php
require_once 'db.php';  // gives you $pdo :contentReference[oaicite:0]{index=0}&#8203;:contentReference[oaicite:1]{index=1}

// NEW: server-side search support
$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $sql = "
      SELECT event_id, title, description, event_date, status
      FROM events
      WHERE status IN ('upcoming','open')
        AND ( title       LIKE :search
           OR description LIKE :search )
      ORDER BY event_date
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ 'search' => "%{$search}%" ]);
} else {
    $stmt = $pdo->query("
      SELECT event_id, title, description, event_date, status
      FROM events
      WHERE status IN ('upcoming','open')
      ORDER BY event_date
    ");
}
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Events</title>
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Events</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item active"><a class="nav-link" href="announcements.html">Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown">More</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Admin Dashboard</a>
                        <a class="dropdown-item" href="#">User Dashboard</a>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="login.html">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.html">Register</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="jumbotron jumbotron-fluid text-center mb-0">
    <div class="container">
        <h1 class="animated-heading">Events</h1>
        <p class="lead">Your gateway to the latest and greatest events</p>
<form method="GET" action="events.php" class="form-inline justify-content-center mt-4">
  <input
    name="q"
    value="<?= htmlspecialchars($search) ?>"
    class="form-control mr-2"
    type="search"
    placeholder="Search events…"
    aria-label="Search"
  >
  <button class="btn btn-light" type="submit">Search</button>
</form>
    </div>
</div>



 <!-- Events Table -->
  <div class="row" id="events">
    <div class="col-12">
      <h2 class="mb-4">All Events</h2>
      <div class="table-responsive">
        <table class="table table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>Event</th>
              <th>Date</th>
              <th>Time</th>
              <th>Countdown</th>
              <th>Description</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($events as $event):
                // 1) Parse the event_date into a DateTime
                $dt  = new DateTime($event['event_date']);

                // 2) Generate an ISO-8601 string WITHOUT a timezone offset,
                //    which JS can reliably parse (YYYY-MM-DDTHH:MM:SS)
                $iso = $dt->format('Y-m-d\\TH:i:s');
            ?>
              <tr>
                <td><?= htmlspecialchars($event['title']       ?? '') ?></td>
                <td><?= htmlspecialchars($dt->format('Y-m-d'))    ?></td>
                <td><?= htmlspecialchars($dt->format('H:i'))      ?></td>
                <td>
                  <!-- 3) Emit the ISO timestamp into data-target -->
                  <span class="countdown" data-target="<?= htmlspecialchars($iso) ?>"></span>
                </td>
                <td><?= htmlspecialchars($event['description'] ?? '') ?></td>
                <td><?= htmlspecialchars($event['status']      ?? '') ?></td>
                <td>
                  <form method="POST" action="register.php" style="display:inline">
                    <input type="hidden" name="event_id" value="<?= (int)$event['event_id'] ?>">
                    <button class="btn btn-success btn-sm">Register</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($events)): ?>
              <tr>
                <td colspan="6" class="text-center">No upcoming events.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<!-- Footer -->
<footer class="footer fixed-bottom bg-dark text-light">
  <div class="container text-center">
    <p class="mb-0">Copyright &copy; 2025 Event Manager.</p>
  </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="countdown.js"></script>


</body>
</html>