<?php
require_once __DIR__ . '/auth.php';
require_login();
$adminUser = current_admin();
$activePage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | <?php echo e(get_setting('site_title', 'Portfolio')); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <a href="dashboard.php" class="text-white text-decoration-none"><i class="bi bi-code-slash me-2"></i>&#64;dev<span class="brand-accent">Admin</span></a>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="<?php echo $activePage === 'dashboard.php' ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="projects.php" class="<?php echo $activePage === 'projects.php' ? 'active' : ''; ?>"><i class="bi bi-folder2-open"></i> Projects</a>
      <a href="services.php" class="<?php echo $activePage === 'services.php' ? 'active' : ''; ?>"><i class="bi bi-briefcase"></i> Services</a>
      <a href="skills.php" class="<?php echo $activePage === 'skills.php' ? 'active' : ''; ?>"><i class="bi bi-bar-chart-line"></i> Skills</a>
      <a href="messages.php" class="<?php echo $activePage === 'messages.php' ? 'active' : ''; ?>">
        <i class="bi bi-envelope"></i> Messages
        <?php $unread = (int)(db()->query('SELECT COUNT(*) c FROM messages WHERE is_read = 0')->fetch_assoc()['c'] ?? 0); ?>
        <?php if ($unread > 0): ?><span class="badge badge-unread"><?php echo $unread; ?></span><?php endif; ?>
      </a>
      <a href="settings.php" class="<?php echo $activePage === 'settings.php' ? 'active' : ''; ?>"><i class="bi bi-gear"></i> Settings</a>
    </nav>
    <div class="sidebar-footer">
      <a href="../index.php" target="_blank"><i class="bi bi-globe2"></i> View Website</a>
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </aside>

  <!-- ============ MAIN ============ -->
  <div class="main-area">
    <header class="topbar">
      <button class="btn btn-sm btn-outline-secondary sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
      <div class="ms-auto d-flex align-items-center gap-3">
        <span class="topbar-date small text-muted d-none d-md-inline"><?php echo date('l, d F Y'); ?></span>
        <div class="dropdown">
          <button class="btn btn-sm dropdown-toggle admin-avatar-btn" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="admin-avatar"><?php echo strtoupper(substr($adminUser['username'] ?? 'A', 0, 1)); ?></span>
            <span class="d-none d-md-inline"><?php echo e($adminUser['username'] ?? 'Admin'); ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-item-text small text-muted"><?php echo e($adminUser['email'] ?? ''); ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-key me-2"></i>Change Password</a></li>
            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main class="content">
