<?php $footerText = get_setting('footer_text', '© ' . date('Y') . ' Web Developer Portfolio'); ?>
<!-- ===================== FOOTER ===================== -->
<footer class="site-footer text-center py-4">
  <div class="container">
    <p class="mb-1 text-muted"><?php echo e($footerText); ?></p>
    <p class="mb-0 small text-muted">Powered by <a class="text-accent" href="https://getbootstrap.com" target="_blank" rel="noopener">Bootstrap</a> &amp; <a class="text-accent" href="https://www.php.net" target="_blank" rel="noopener">PHP/MySQL</a> · Admin: <a class="text-accent" href="<?php echo SITE_URL; ?>/admin/">Login</a></p>
  </div>
</footer>

<a href="#top" class="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
