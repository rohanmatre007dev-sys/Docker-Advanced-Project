<?php
// php-app/src/Views/layout.php
/** Expects $title and $body */
if (!isset($title)) $title = 'Smart Support Portal';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="/">Smart Support</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="/tickets">Tickets</a></li>
          <li class="nav-item"><a class="nav-link" href="/tickets/new">New Ticket</a></li>
          <li class="nav-item"><a class="nav-link" href="/logout">Logout (<?= htmlspecialchars($_SESSION['email']) ?>)</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <?php if (!empty($message ?? null)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <?= $body ?>
</div>

<footer class="text-muted text-center mt-5 mb-3"><small>Smart Support Portal</small></footer>
</body>
</html>
