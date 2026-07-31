    </main>
    <footer class="admin-footer text-center text-muted small py-3">
      © <?php echo date('Y'); ?> <?php echo e(get_setting('site_title', 'Portfolio')); ?> · Admin Panel · Built with PHP, MySQL, Bootstrap &amp; Angular.js
    </footer>
  </div>
</div>

<?php $msg = flash('admin_success'); ?>
<?php if ($msg): ?>
  <div class="toast-notification success" id="toastSuccess"><i class="bi bi-check-circle-fill me-2"></i><?php echo e($msg); ?></div>
<?php endif; ?>
<?php $err = flash('admin_error'); ?>
<?php if ($err): ?>
  <div class="toast-notification error" id="toastError"><i class="bi bi-x-circle-fill me-2"></i><?php echo e($err); ?></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('collapsed');
  });
</script>
</body>
</html>
