<?php
require_once __DIR__ . '/includes/header.php';

// ---------- Handle password change ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPass = $_POST['current_password'] ?? '';
    $newPass     = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    $admin = fetch_one('SELECT * FROM admin_users WHERE id = ?', 'i', [(int)$_SESSION['admin_id']]);

    if (!password_verify($currentPass, $admin['password'])) {
        flash('admin_error', 'Current password is incorrect.');
    } elseif (strlen($newPass) < 6) {
        flash('admin_error', 'New password must be at least 6 characters.');
    } elseif ($newPass !== $confirmPass) {
        flash('admin_error', 'New passwords do not match.');
    } else {
        execute_query('UPDATE admin_users SET password = ? WHERE id = ?', 'si', [password_hash($newPass, PASSWORD_BCRYPT), (int)$_SESSION['admin_id']]);
        flash('admin_success', 'Password changed successfully.');
    }
    redirect('settings.php');
}

// ---------- Handle settings update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    try {
        $values = $_POST['settings'] ?? [];

        if (isset($_FILES['about_photo']) && $_FILES['about_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $photo = handle_upload($_FILES['about_photo'], UPLOAD_DIR);
            if ($photo) {
                $oldPhoto = get_setting('about_photo', '');
                if ($oldPhoto && file_exists(UPLOAD_DIR . $oldPhoto)) {
                    @unlink(UPLOAD_DIR . $oldPhoto);
                }
                $values['about_photo'] = $photo;
            }
        }

        foreach ($values as $key => $value) {
            $key = preg_replace('/[^a-z0-9_]/i', '', $key);
            $value = trim((string)$value);
            $exists = fetch_one('SELECT id FROM settings WHERE setting_key = ?', 's', [$key]);
            if ($exists) {
                execute_query('UPDATE settings SET setting_value = ? WHERE setting_key = ?', 'ss', [$value, $key]);
            } else {
                execute_query('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', 'ss', [$key, $value]);
            }
        }
        flash('admin_success', 'Settings saved successfully.');
    } catch (Exception $ex) {
        flash('admin_error', $ex->getMessage());
    }
    redirect('settings.php');
}

$allSettings = fetch_all('SELECT setting_key, setting_value FROM settings');
$settings = [];
foreach ($allSettings as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Settings</h1>
  <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-globe2 me-1"></i> Preview Website</a>
</div>

<?php if ($msg = flash('admin_success')): ?>
  <div class="alert alert-success py-2 small"><i class="bi bi-check-circle-fill me-2"></i><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if ($err = flash('admin_error')): ?>
  <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($err); ?></div>
<?php endif; ?>

<ul class="nav nav-tabs settings-tabs mb-4" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabGeneral" type="button"><i class="bi bi-person-badge me-1"></i>General</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabContent" type="button"><i class="bi bi-card-text me-1"></i>Content</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabContact" type="button"><i class="bi bi-link-45deg me-1"></i>Contact & Social</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPassword" type="button"><i class="bi bi-key me-1"></i>Password</button></li>
</ul>

<div class="tab-content">
  <!-- ============ GENERAL ============ -->
  <div class="tab-pane fade show active" id="tabGeneral">
    <form method="post" enctype="multipart/form-data" class="panel">
      <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-person-badge me-2 text-primary"></i>Profile & Hero</h5></div>
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Site Title</label>
            <input type="text" name="settings[site_title]" class="form-control" value="<?php echo e($settings['site_title'] ?? ''); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Greeting</label>
            <input type="text" name="settings[hero_greeting]" class="form-control" value="<?php echo e($settings['hero_greeting'] ?? ''); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Your Name</label>
            <input type="text" name="settings[hero_name]" class="form-control" value="<?php echo e($settings['hero_name'] ?? ''); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Your Role</label>
            <input type="text" name="settings[hero_role]" class="form-control" value="<?php echo e($settings['hero_role'] ?? ''); ?>">
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Hero Tagline</label>
            <textarea name="settings[hero_tagline]" class="form-control" rows="2"><?php echo e($settings['hero_tagline'] ?? ''); ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Hero Button Text</label>
            <input type="text" name="settings[hero_btn_text]" class="form-control" value="<?php echo e($settings['hero_btn_text'] ?? ''); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Profile Photo</label>
            <input type="file" name="about_photo" class="form-control" accept="image/*">
            <?php if (!empty($settings['about_photo'])): ?>
              <small class="text-muted">Current: <img src="<?php echo SITE_URL . '/admin/uploads/' . e($settings['about_photo']); ?>" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:50%;vertical-align:middle;margin-left:6px;"></small>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="panel-foot"><button type="submit" name="save_settings" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Settings</button></div>
    </form>
  </div>

  <!-- ============ CONTENT ============ -->
  <div class="tab-pane fade" id="tabContent">
    <form method="post" class="panel">
      <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-card-text me-2 text-primary"></i>About & Resume</h5></div>
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">About Title</label>
            <input type="text" name="settings[about_title]" class="form-control" value="<?php echo e($settings['about_title'] ?? ''); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Resume Title</label>
            <input type="text" name="settings[resume_title]" class="form-control" value="<?php echo e($settings['resume_title'] ?? ''); ?>">
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">About Text</label>
            <textarea name="settings[about_text]" class="form-control" rows="4"><?php echo e($settings['about_text'] ?? ''); ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Resume Text</label>
            <textarea name="settings[resume_text]" class="form-control" rows="2"><?php echo e($settings['resume_text'] ?? ''); ?></textarea>
          </div>
        </div>
      </div>
      <div class="panel-head border-0"><h5 class="mb-0 fw-semibold"><i class="bi bi-briefcase me-2 text-primary"></i>Experience</h5></div>
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label small fw-semibold">Period 1</label><input type="text" name="settings[experience_1_year]" class="form-control" value="<?php echo e($settings['experience_1_year'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Role 1</label><input type="text" name="settings[experience_1_role]" class="form-control" value="<?php echo e($settings['experience_1_role'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Text 1</label><input type="text" name="settings[experience_1_text]" class="form-control" value="<?php echo e($settings['experience_1_text'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Period 2</label><input type="text" name="settings[experience_2_year]" class="form-control" value="<?php echo e($settings['experience_2_year'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Role 2</label><input type="text" name="settings[experience_2_role]" class="form-control" value="<?php echo e($settings['experience_2_role'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Text 2</label><input type="text" name="settings[experience_2_text]" class="form-control" value="<?php echo e($settings['experience_2_text'] ?? ''); ?>"></div>
        </div>
      </div>
      <div class="panel-head border-0"><h5 class="mb-0 fw-semibold"><i class="bi bi-mortarboard me-2 text-primary"></i>Education</h5></div>
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label small fw-semibold">Period</label><input type="text" name="settings[education_1_year]" class="form-control" value="<?php echo e($settings['education_1_year'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Degree</label><input type="text" name="settings[education_1_role]" class="form-control" value="<?php echo e($settings['education_1_role'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Text</label><input type="text" name="settings[education_1_text]" class="form-control" value="<?php echo e($settings['education_1_text'] ?? ''); ?>"></div>
        </div>
      </div>
      <div class="panel-foot"><button type="submit" name="save_settings" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Settings</button></div>
    </form>
  </div>

  <!-- ============ CONTACT & SOCIAL ============ -->
  <div class="tab-pane fade" id="tabContact">
    <form method="post" class="panel">
      <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-link-45deg me-2 text-primary"></i>Contact & Social Links</h5></div>
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label small fw-semibold">Email</label><input type="email" name="settings[email]" class="form-control" value="<?php echo e($settings['email'] ?? ''); ?>"></div>
          <div class="col-md-6"><label class="form-label small fw-semibold">Phone</label><input type="text" name="settings[phone]" class="form-control" value="<?php echo e($settings['phone'] ?? ''); ?>"></div>
          <div class="col-md-6"><label class="form-label small fw-semibold">Location</label><input type="text" name="settings[location]" class="form-control" value="<?php echo e($settings['location'] ?? ''); ?>"></div>
          <div class="col-md-6"><label class="form-label small fw-semibold">Footer Text</label><input type="text" name="settings[footer_text]" class="form-control" value="<?php echo e($settings['footer_text'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">GitHub</label><input type="text" name="settings[social_github]" class="form-control" value="<?php echo e($settings['social_github'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">LinkedIn</label><input type="text" name="settings[social_linkedin]" class="form-control" value="<?php echo e($settings['social_linkedin'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Twitter / X</label><input type="text" name="settings[social_twitter]" class="form-control" value="<?php echo e($settings['social_twitter'] ?? ''); ?>"></div>
          <div class="col-md-6"><label class="form-label small fw-semibold">Contact Title</label><input type="text" name="settings[contact_title]" class="form-control" value="<?php echo e($settings['contact_title'] ?? ''); ?>"></div>
          <div class="col-md-6"><label class="form-label small fw-semibold">Contact Subtitle</label><input type="text" name="settings[contact_subtitle]" class="form-control" value="<?php echo e($settings['contact_subtitle'] ?? ''); ?>"></div>
        </div>
      </div>
      <div class="panel-foot"><button type="submit" name="save_settings" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Settings</button></div>
    </form>
  </div>

  <!-- ============ PASSWORD ============ -->
  <div class="tab-pane fade" id="tabPassword">
    <form method="post" class="panel">
      <div class="panel-head"><h5 class="mb-0 fw-semibold"><i class="bi bi-key me-2 text-primary"></i>Change Password</h5></div>
      <div class="panel-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
        </div>
        <div class="row g-3 mt-0">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">New Password</label>
            <input type="password" name="new_password" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="panel-foot"><button type="submit" name="change_password" class="btn btn-danger px-4"><i class="bi bi-shield-lock me-1"></i>Change Password</button></div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
