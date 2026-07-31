<?php
require_once __DIR__ . '/includes/header.php';

$action = $_POST['action'] ?? '';
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'read') {
        execute_query('UPDATE messages SET is_read = 1 WHERE id = ?', 'i', [(int)$_POST['id']]);
        flash('admin_success', 'Message marked as read.');
    } elseif ($action === 'unread') {
        execute_query('UPDATE messages SET is_read = 0 WHERE id = ?', 'i', [(int)$_POST['id']]);
        flash('admin_success', 'Message marked as unread.');
    } elseif ($action === 'delete') {
        execute_query('DELETE FROM messages WHERE id = ?', 'i', [(int)$_POST['id']]);
        flash('admin_success', 'Message deleted.');
    }
    redirect('messages.php');
}

if ($viewId > 0) {
    execute_query('UPDATE messages SET is_read = 1 WHERE id = ?', 'i', [$viewId]);
}
$viewing = $viewId > 0 ? fetch_one('SELECT * FROM messages WHERE id = ?', 'i', [$viewId]) : null;

$messages = fetch_all('SELECT * FROM messages ORDER BY is_read ASC, id DESC');
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Messages</h1>
  <span class="badge text-bg-primary px-3 py-2"><?php echo count(array_filter($messages, fn($m) => $m['is_read'] == 0)); ?> unread</span>
</div>

<?php if ($msg = flash('admin_success')): ?>
  <div class="alert alert-success py-2 small"><i class="bi bi-check-circle-fill me-2"></i><?php echo e($msg); ?></div>
<?php endif; ?>

<div class="row g-3">
  <!-- Inbox list -->
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-inbox me-2 text-primary"></i>Inbox</h5></div>
      <div class="panel-body p-0">
        <div class="message-list">
          <?php if (!$messages): ?>
            <p class="text-center text-muted small py-4 mb-0">No messages yet.</p>
          <?php endif; ?>
          <?php foreach ($messages as $m): ?>
            <a href="messages.php?view=<?php echo $m['id']; ?>" class="message-item <?php echo $viewId === (int)$m['id'] ? 'active' : ''; ?> <?php echo $m['is_read'] == 0 ? 'unread' : ''; ?>">
              <div class="d-flex justify-content-between">
                <strong><?php echo e($m['name']); ?></strong>
                <small class="text-muted"><?php echo format_date($m['created_at']); ?></small>
              </div>
              <div class="small text-muted text-truncate"><?php echo e($m['subject']); ?></div>
              <div class="small text-muted text-truncate"><?php echo e($m['email']); ?></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Message detail -->
  <div class="col-lg-7">
    <div class="panel h-100">
      <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-envelope-open me-2 text-primary"></i>Message</h5></div>
      <div class="panel-body">
        <?php if ($viewing): ?>
          <div class="d-flex justify-content-between flex-wrap align-items-start gap-2 mb-3">
            <div>
              <h5 class="mb-1"><?php echo e($viewing['subject']); ?></h5>
              <small class="text-muted">From <strong><?php echo e($viewing['name']); ?></strong> &lt;<?php echo e($viewing['email']); ?>&gt;</small>
            </div>
            <span class="badge <?php echo $viewing['is_read'] == 1 ? 'text-bg-secondary' : 'text-bg-success'; ?>"><?php echo $viewing['is_read'] == 1 ? 'Read' : 'New'; ?></span>
          </div>
          <hr>
          <p class="mb-4" style="white-space:pre-wrap"><?php echo e($viewing['message']); ?></p>
          <div class="d-flex gap-2">
            <a href="mailto:<?php echo e($viewing['email']); ?>?subject=<?php echo rawurlencode('Re: ' . $viewing['subject']); ?>" class="btn btn-primary btn-sm"><i class="bi bi-reply me-1"></i>Reply</a>
            <form method="post" class="d-inline">
              <input type="hidden" name="action" value="<?php echo $viewing['is_read'] == 1 ? 'unread' : 'read'; ?>">
              <input type="hidden" name="id" value="<?php echo $viewing['id']; ?>">
              <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-envelope me-1"></i><?php echo $viewing['is_read'] == 1 ? 'Mark Unread' : 'Mark Read'; ?></button>
            </form>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this message?');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo $viewing['id']; ?>">
              <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
          </div>
        <?php else: ?>
          <p class="text-center text-muted py-5 mb-0"><i class="bi bi-envelope d-block mb-2" style="font-size:2.5rem;"></i>Select a message to read it.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
