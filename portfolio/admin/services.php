<?php
require_once __DIR__ . '/includes/header.php';

$action = $_POST['action'] ?? '';
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add' || $action === 'update') {
            $title       = trim($_POST['title'] ?? '');
            $icon        = trim($_POST['icon'] ?? 'bi-code-slash');
            $description = trim($_POST['description'] ?? '');
            $sortOrder   = (int)($_POST['sort_order'] ?? 0);

            if ($title === '') {
                throw new RuntimeException('Service title is required.');
            }

            if ($action === 'add') {
                execute_query('INSERT INTO services (title, icon, description, sort_order) VALUES (?, ?, ?, ?)', 'sssi', [$title, $icon, $description, $sortOrder]);
                flash('admin_success', 'Service added.');
            } else {
                execute_query('UPDATE services SET title = ?, icon = ?, description = ?, sort_order = ? WHERE id = ?', 'sssii', [$title, $icon, $description, $sortOrder, (int)$_POST['id']]);
                flash('admin_success', 'Service updated.');
            }
        } elseif ($action === 'delete') {
            execute_query('DELETE FROM services WHERE id = ?', 'i', [(int)$_POST['id']]);
            flash('admin_success', 'Service deleted.');
        }
    } catch (Exception $ex) {
        flash('admin_error', $ex->getMessage());
    }
    redirect('services.php' . (($editId) ? '?edit=' . $editId : ''));
}

$services = fetch_all('SELECT * FROM services ORDER BY sort_order ASC, id ASC');
if ($editId > 0) {
    $editing = fetch_one('SELECT * FROM services WHERE id = ?', 'i', [$editId]);
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Services</h1>
  <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#serviceForm" aria-expanded="<?php echo $editing ? 'true' : 'false'; ?>">
    <i class="bi bi-plus-lg me-1"></i><?php echo $editing ? 'Edit Service' : 'Add New Service'; ?>
  </button>
</div>

<?php if ($msg = flash('admin_success')): ?>
  <div class="alert alert-success py-2 small"><i class="bi bi-check-circle-fill me-2"></i><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if ($err = flash('admin_error')): ?>
  <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($err); ?></div>
<?php endif; ?>

<div class="collapse <?php echo $editing ? 'show' : ''; ?>" id="serviceForm">
  <div class="panel mb-4">
    <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-briefcase me-2 text-primary"></i><?php echo $editing ? 'Edit Service' : 'New Service'; ?></h5></div>
    <div class="panel-body">
      <form method="post">
        <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'add'; ?>">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo $editing['id']; ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e($editing['title'] ?? ''); ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Icon (Bootstrap Icons)</label>
            <input type="text" name="icon" class="form-control" value="<?php echo e($editing['icon'] ?? 'bi-code-slash'); ?>" placeholder="bi-code-slash">
            <small class="text-muted">See <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a></small>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?php echo e($editing['sort_order'] ?? '0'); ?>">
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="2"><?php echo e($editing['description'] ?? ''); ?></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i><?php echo $editing ? 'Update Service' : 'Add Service'; ?></button>
            <?php if ($editing): ?><a href="services.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-collection me-2 text-primary"></i>All Services (<?php echo count($services); ?>)</h5></div>
  <div class="panel-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 admin-table">
        <thead>
          <tr><th>#</th><th>Icon</th><th>Title</th><th>Description</th><th style="width:140px">Actions</th></tr>
        </thead>
        <tbody>
          <?php if (!$services): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No services added yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($services as $s): ?>
            <tr>
              <td class="text-muted"><?php echo $s['sort_order']; ?></td>
              <td><span class="service-icon-preview"><i class="bi <?php echo e($s['icon']); ?>"></i></span></td>
              <td><strong><?php echo e($s['title']); ?></strong></td>
              <td class="small text-muted"><?php echo e($s['description']); ?></td>
              <td>
                <a href="services.php?edit=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this service?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
