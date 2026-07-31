<?php
require_once __DIR__ . '/includes/header.php';

$action = $_POST['action'] ?? '';
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;

// ---------- Handle actions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add' || $action === 'update') {
            $title       = trim($_POST['title'] ?? '');
            $category    = trim($_POST['category'] ?? '');
            $techStack   = trim($_POST['tech_stack'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $url         = trim($_POST['url'] ?? '');

            if ($title === '' || $category === '') {
                throw new RuntimeException('Title and category are required.');
            }

            $image = isset($_FILES['image']) ? handle_upload($_FILES['image'], UPLOAD_DIR) : null;

            if ($action === 'add') {
                execute_query(
                    'INSERT INTO projects (title, category, tech_stack, description, image, url) VALUES (?, ?, ?, ?, ?, ?)',
                    'ssssss',
                    [$title, $category, $techStack, $description, $image, $url]
                );
                flash('admin_success', 'Project added successfully.');
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $current = fetch_one('SELECT image FROM projects WHERE id = ?', 'i', [$id]);
                $imageName = $image ?? $current['image'] ?? null;

                // If a new image was uploaded, remove the old one
                if ($image && $current && $current['image'] && file_exists(UPLOAD_DIR . $current['image'])) {
                    @unlink(UPLOAD_DIR . $current['image']);
                }

                execute_query(
                    'UPDATE projects SET title = ?, category = ?, tech_stack = ?, description = ?, image = ?, url = ? WHERE id = ?',
                    'ssssssi',
                    [$title, $category, $techStack, $description, $imageName, $url, $id]
                );
                flash('admin_success', 'Project updated successfully.');
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $current = fetch_one('SELECT image FROM projects WHERE id = ?', 'i', [$id]);
            if ($current && $current['image'] && file_exists(UPLOAD_DIR . $current['image'])) {
                @unlink(UPLOAD_DIR . $current['image']);
            }
            execute_query('DELETE FROM projects WHERE id = ?', 'i', [$id]);
            flash('admin_success', 'Project deleted.');
        }
    } catch (Exception $ex) {
        flash('admin_error', $ex->getMessage());
    }
    redirect('projects.php' . (($editId) ? '?edit=' . $editId : ''));
}

$projects = fetch_all('SELECT * FROM projects ORDER BY id DESC');

if ($editId > 0) {
    $editing = fetch_one('SELECT * FROM projects WHERE id = ?', 'i', [$editId]);
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Projects</h1>
  <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#projectForm" aria-expanded="<?php echo $editing ? 'true' : 'false'; ?>">
    <i class="bi bi-plus-lg me-1"></i><?php echo $editing ? 'Edit Project' : 'Add New Project'; ?>
  </button>
</div>

<?php if ($msg = flash('admin_success')): ?>
  <div class="alert alert-success py-2 small"><i class="bi bi-check-circle-fill me-2"></i><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if ($err = flash('admin_error')): ?>
  <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($err); ?></div>
<?php endif; ?>

<!-- ============ Add / Edit form ============ -->
<div class="collapse <?php echo $editing ? 'show' : ''; ?>" id="projectForm">
  <div class="panel mb-4">
    <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-folder-plus me-2 text-primary"></i><?php echo $editing ? 'Edit Project' : 'New Project'; ?></h5></div>
    <div class="panel-body">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'add'; ?>">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo $editing['id']; ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e($editing['title'] ?? ''); ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">Category *</label>
            <input type="text" name="category" class="form-control" value="<?php echo e($editing['category'] ?? ''); ?>" placeholder="e.g. Web App" required>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">URL</label>
            <input type="text" name="url" class="form-control" value="<?php echo e($editing['url'] ?? ''); ?>" placeholder="https://...">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Tech Stack</label>
            <input type="text" name="tech_stack" class="form-control" value="<?php echo e($editing['tech_stack'] ?? ''); ?>" placeholder="HTML, CSS, PHP, MySQL">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <?php if ($editing && $editing['image']): ?>
              <small class="text-muted">Current: <img src="<?php echo SITE_URL . '/admin/uploads/' . e($editing['image']); ?>" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:6px;vertical-align:middle;margin-left:6px;"></small>
            <?php endif; ?>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo e($editing['description'] ?? ''); ?></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i><?php echo $editing ? 'Update Project' : 'Add Project'; ?></button>
            <?php if ($editing): ?><a href="projects.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============ Project list ============ -->
<div class="panel">
  <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-collection me-2 text-primary"></i>All Projects (<?php echo count($projects); ?>)</h5></div>
  <div class="panel-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 admin-table">
        <thead>
          <tr>
            <th style="width:70px">Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Tech Stack</th>
            <th>Date</th>
            <th style="width:140px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$projects): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No projects yet. Click "Add New Project" to create one.</td></tr>
          <?php endif; ?>
          <?php foreach ($projects as $p): ?>
            <tr>
              <td>
                <?php if ($p['image']): ?>
                  <img src="<?php echo SITE_URL . '/admin/uploads/' . e($p['image']); ?>" alt="" class="thumb-sm">
                <?php else: ?>
                  <span class="thumb-sm d-inline-flex align-items-center justify-content-center text-muted" style="background:var(--bg-card)"><i class="bi bi-image"></i></span>
                <?php endif; ?>
              </td>
              <td><strong><?php echo e($p['title']); ?></strong></td>
              <td><span class="badge text-bg-primary-subtle text-primary-emphasis"><?php echo e($p['category']); ?></span></td>
              <td class="small text-muted"><?php echo e($p['tech_stack']); ?></td>
              <td class="small text-muted"><?php echo format_date($p['created_at']); ?></td>
              <td>
                <a href="projects.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this project?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
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
