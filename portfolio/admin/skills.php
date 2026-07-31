<?php
require_once __DIR__ . '/includes/header.php';

$action = $_POST['action'] ?? '';
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add' || $action === 'update') {
            $name     = trim($_POST['name'] ?? '');
            $percent  = max(0, min(100, (int)($_POST['percent'] ?? 0)));
            $category = trim($_POST['category'] ?? 'Web');

            if ($name === '') {
                throw new RuntimeException('Skill name is required.');
            }

            if ($action === 'add') {
                execute_query('INSERT INTO skills (name, percent, category) VALUES (?, ?, ?)', 'sis', [$name, $percent, $category]);
                flash('admin_success', 'Skill added.');
            } else {
                execute_query('UPDATE skills SET name = ?, percent = ?, category = ? WHERE id = ?', 'sisi', [$name, $percent, $category, (int)$_POST['id']]);
                flash('admin_success', 'Skill updated.');
            }
        } elseif ($action === 'delete') {
            execute_query('DELETE FROM skills WHERE id = ?', 'i', [(int)$_POST['id']]);
            flash('admin_success', 'Skill deleted.');
        }
    } catch (Exception $ex) {
        flash('admin_error', $ex->getMessage());
    }
    redirect('skills.php' . (($editId) ? '?edit=' . $editId : ''));
}

$skills = fetch_all('SELECT * FROM skills ORDER BY category ASC, percent DESC');
if ($editId > 0) {
    $editing = fetch_one('SELECT * FROM skills WHERE id = ?', 'i', [$editId]);
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Skills</h1>
  <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#skillForm" aria-expanded="<?php echo $editing ? 'true' : 'false'; ?>">
    <i class="bi bi-plus-lg me-1"></i><?php echo $editing ? 'Edit Skill' : 'Add New Skill'; ?>
  </button>
</div>

<?php if ($msg = flash('admin_success')): ?>
  <div class="alert alert-success py-2 small"><i class="bi bi-check-circle-fill me-2"></i><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if ($err = flash('admin_error')): ?>
  <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($err); ?></div>
<?php endif; ?>

<div class="collapse <?php echo $editing ? 'show' : ''; ?>" id="skillForm">
  <div class="panel mb-4">
    <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-line me-2 text-primary"></i><?php echo $editing ? 'Edit Skill' : 'New Skill'; ?></h5></div>
    <div class="panel-body">
      <form method="post">
        <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'add'; ?>">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo $editing['id']; ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label small fw-semibold">Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo e($editing['name'] ?? ''); ?>" placeholder="e.g. PHP" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Percentage (0-100)</label>
            <input type="number" name="percent" class="form-control" min="0" max="100" value="<?php echo e($editing['percent'] ?? '85'); ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">Category</label>
            <select name="category" class="form-select">
              <?php foreach (['Web', 'Backend', 'CMS', 'Design'] as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo isset($editing) && $editing['category'] === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i><?php echo $editing ? 'Update Skill' : 'Add Skill'; ?></button>
            <?php if ($editing): ?><a href="skills.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-collection me-2 text-primary"></i>All Skills (<?php echo count($skills); ?>)</h5></div>
  <div class="panel-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 admin-table">
        <thead>
          <tr><th>Skill</th><th>Category</th><th style="width:40%">Level</th><th style="width:140px">Actions</th></tr>
        </thead>
        <tbody>
          <?php if (!$skills): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">No skills added yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($skills as $s): ?>
            <tr>
              <td><strong><?php echo e($s['name']); ?></strong></td>
              <td><span class="badge text-bg-info-subtle text-info-emphasis"><?php echo e($s['category']); ?></span></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="chart-track flex-grow-1">
                    <div class="chart-fill skill-fill" style="width:<?php echo (int)$s['percent']; ?>%"></div>
                  </div>
                  <small class="text-muted"><?php echo (int)$s['percent']; ?>%</small>
                </div>
              </td>
              <td>
                <a href="skills.php?edit=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this skill?');">
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
