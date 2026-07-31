<?php
require_once __DIR__ . '/includes/auth.php';

// Already logged in? Go to dashboard.
if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $user = fetch_one('SELECT * FROM admin_users WHERE username = ?', 's', [$username]);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$user['id'];
            $_SESSION['admin_username'] = $user['username'];
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-body">

  <div class="login-wrap">
    <div class="login-card">
      <div class="login-logo">
        <i class="bi bi-code-slash"></i>
      </div>
      <h1 class="h4 text-center mb-1 fw-bold">Admin Panel</h1>
      <p class="text-center text-muted small mb-4">Sign in to manage your portfolio</p>

      <?php if ($error !== ''): ?>
        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" class="form-control" placeholder="admin" value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label small fw-semibold">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            <button type="button" class="btn btn-outline-secondary" id="togglePass" tabindex="-1"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In <i class="bi bi-box-arrow-in-right ms-1"></i></button>
      </form>

      <p class="text-center small text-muted mt-4 mb-0">Default login: <code>admin</code> / <code>admin123</code></p>
      <a href="../index.php" class="btn btn-sm btn-link w-100 text-center mt-1"><i class="bi bi-arrow-left me-1"></i> Back to website</a>
    </div>
  </div>

<script>
  document.getElementById('togglePass')?.addEventListener('click', function () {
    var input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
  });
</script>
</body>
</html>
