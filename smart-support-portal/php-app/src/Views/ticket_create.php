<?php
// php-app/src/Views/ticket_create.php
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h2>Create Ticket</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/tickets">
      <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input id="title" name="title" type="text" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="6" class="form-control" required></textarea>
      </div>
      <button class="btn btn-primary" type="submit">Create Ticket</button>
      <a class="btn btn-link" href="/tickets">Back</a>
    </form>
  </div>
</div>
