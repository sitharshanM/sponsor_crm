<?php
// public/sponsors.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';

$sponsorModel = new Sponsor($pdo);
$sponsors = $sponsorModel->all();

$pageTitle = 'All Sponsors';
include __DIR__ . '/../includes/header.php';
?>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">
      <i class="fas fa-users"></i> All Sponsors
    </h2>
    <a href="/add_sponsor.php" class="btn btn-primary">
      <i class="fas fa-plus"></i> Add Sponsor
    </a>
  </div>

  <?php if(empty($sponsors)): ?>
    <div class="empty-state">
      <div class="empty-state-icon">
        <i class="fas fa-inbox"></i>
      </div>
      <div class="empty-state-title">No Sponsors Yet</div>
      <div class="empty-state-text">Get started by adding your first sponsor</div>
      <a href="/add_sponsor.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add First Sponsor
      </a>
    </div>
  <?php else: ?>
    <div class="search-filter-bar">
      <div class="search-box">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="form-control search-input" placeholder="Search sponsors...">
      </div>
      <select class="form-control filter-select" id="statusFilter" style="max-width: 200px;">
        <option value="">All Status</option>
        <option value="new">New</option>
        <option value="interested">Interested</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>

    <div class="table-container">
      <table id="sponsorsTable">
        <thead>
          <tr>
            <th>Company</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Type</th>
            <th>Status</th>
            <th>Added</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($sponsors as $s): ?>
            <tr data-status="<?=htmlspecialchars($s['status'])?>">
              <td>
                <strong><?=htmlspecialchars($s['company_name'])?></strong>
                <?php if($s['industry']): ?>
                  <br><small style="color: var(--gray-500);"><?=htmlspecialchars($s['industry'])?></small>
                <?php endif; ?>
              </td>
              <td><?=htmlspecialchars($s['contact_person'] ?: '-')?></td>
              <td>
                <?php if($s['email']): ?>
                  <a href="mailto:<?=htmlspecialchars($s['email'])?>"><?=htmlspecialchars($s['email'])?></a>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td><?=htmlspecialchars($s['sponsor_type'] ?: '-')?></td>
              <td>
                <span class="badge badge-<?=str_replace('_', '-', $s['status'])?>">
                  <?=ucfirst(str_replace('_', ' ', $s['status']))?>
                </span>
              </td>
              <td><?=date('M d, Y', strtotime($s['added_on']))?></td>
              <td>
                <div class="action-buttons">
                  <a href="/view_sponsor.php?id=<?=$s['id']?>" class="action-btn action-btn-view" data-tooltip="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="/edit_sponsor.php?id=<?=$s['id']?>" class="action-btn action-btn-edit" data-tooltip="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
// Status filter
document.getElementById('statusFilter')?.addEventListener('change', function() {
    const filter = this.value;
    const rows = document.querySelectorAll('#sponsorsTable tbody tr');
    rows.forEach(row => {
        if (!filter || row.dataset.status === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
