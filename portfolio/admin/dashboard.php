<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <div>
    <h1 class="h3 fw-bold mb-0">Dashboard</h1>
    <p class="text-muted small mb-0">Welcome back, <?php echo e($_SESSION['admin_username'] ?? 'Admin'); ?> — here's your portfolio overview.</p>
  </div>
  <a href="projects.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Project</a>
</div>

<!-- ============ Angular.js powered section ============ -->
<div ng-app="adminApp" ng-controller="DashboardCtrl" ng-cloak>

  <!-- Stat cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card stat-purple">
        <div class="stat-icon"><i class="bi bi-folder2-open"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ stats.counts.projects || 0 }}</span>
          <span class="stat-label">Projects</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card stat-cyan">
        <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ stats.counts.services || 0 }}</span>
          <span class="stat-label">Services</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card stat-orange">
        <div class="stat-icon"><i class="bi bi-bar-chart-line"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ stats.counts.skills || 0 }}</span>
          <span class="stat-label">Skills</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card stat-green">
        <div class="stat-icon"><i class="bi bi-envelope"></i></div>
        <div class="stat-info">
          <span class="stat-value">{{ stats.counts.unread || 0 }}</span>
          <span class="stat-label">Unread Messages</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <!-- Projects by category (bar chart, rendered by Angular) -->
    <div class="col-lg-6">
      <div class="panel h-100">
        <div class="panel-head">
          <h5 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Projects by Category</h5>
        </div>
        <div class="panel-body">
          <div ng-repeat="item in stats.projects_by_category" class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
              <span class="fw-semibold">{{ item.category }}</span>
              <span class="text-muted">{{ item.count }} project{{ item.count === 1 ? '' : 's' }}</span>
            </div>
            <div class="chart-track">
              <div class="chart-fill" ng-style="{ 'width': chartWidth(item) }"></div>
            </div>
          </div>
          <p ng-if="!stats.projects_by_category.length" class="text-muted small mb-0">No projects yet.</p>
        </div>
      </div>
    </div>

    <!-- Top skills -->
    <div class="col-lg-6">
      <div class="panel h-100">
        <div class="panel-head">
          <h5 class="mb-0 fw-semibold"><i class="bi bi-lightning-charge me-2 text-warning"></i>Top Skills</h5>
        </div>
        <div class="panel-body">
          <div ng-repeat="skill in stats.skills | limitTo: 5" class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
              <span class="fw-semibold">{{ skill.name }}</span>
              <span class="text-muted">{{ skill.percent }}%</span>
            </div>
            <div class="chart-track">
              <div class="chart-fill skill-fill" ng-style="{ 'width': skill.percent + '%' }"></div>
            </div>
          </div>
          <p ng-if="!stats.skills.length" class="text-muted small mb-0">No skills added yet.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent messages -->
  <div class="panel">
    <div class="panel-head d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold"><i class="bi bi-inbox me-2 text-primary"></i>Recent Messages</h5>
      <a href="messages.php" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="panel-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="m in stats.recent_messages">
              <td>
                <strong>{{ m.name }}</strong>
                <div class="small text-muted">{{ m.email }}</div>
              </td>
              <td>{{ m.subject }}</td>
              <td class="text-muted small">{{ m.created_at }}</td>
              <td>
                <span class="badge" ng-class="m.is_read == 1 ? 'text-bg-secondary' : 'text-bg-success'">
                  {{ m.is_read == 1 ? 'Read' : 'New' }}
                </span>
              </td>
            </tr>
            <tr ng-if="!stats.recent_messages.length">
              <td colspan="4" class="text-center text-muted py-4">No messages yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
