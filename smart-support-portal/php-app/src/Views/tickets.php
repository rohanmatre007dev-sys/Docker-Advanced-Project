<?php
// php-app/src/Views/tickets.php
?>
<h2>My Tickets</h2>
<?php if (empty($tickets)): ?>
  <p>No tickets yet. <a href="/tickets/new">Create one</a>.</p>
<?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Title</th><th>Created</th><th>Priority</th><th>Category</th><th>Confidence</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $t): ?>
        <tr>
          <td><?= htmlspecialchars($t['title']) ?></td>
          <td><?= htmlspecialchars($t['created_at']) ?></td>
          <td><?= htmlspecialchars($t['priority'] ?? '-') ?></td>
          <td><?= htmlspecialchars($t['category'] ?? '-') ?></td>
          <td><?= isset($t['confidence']) ? number_format((float)$t['confidence'], 2) : '-' ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
