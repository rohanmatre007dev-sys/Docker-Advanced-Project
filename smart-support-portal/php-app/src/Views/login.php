<?php
// php-app/src/Views/login.php
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/login">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="<?= htmlspecialchars($email ?? '') ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" class="form-control" required>
      </div>
      <button class="btn btn-primary" type="submit">Login</button>
      <a class="btn btn-link" href="/register">Register</a>
    </form>
  </div>
</div>
