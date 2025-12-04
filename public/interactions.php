<?php
// public/interactions.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Interaction.php';
require_once __DIR__ . '/../src/Sponsor.php';

$interactionModel = new Interaction($pdo);
$sponsorModel = new Sponsor($pdo);

$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$stmt = $pdo->query("
    SELECT i.*, s.company_name, s.contact_person 
    FROM interactions i 
    JOIN sponsors s ON i.sponsor_id = s.id 
    ORDER BY i.created_on DESC
");
$interactions = $stmt->fetchAll();

$pageTitle = 'All Interactions';
include __DIR__ . '/../includes/header.php';
?>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">
      <i class="fas fa-comments"></i> All Interactions
    </h2>
    <div style="color: var(--gray-600);">
      Total: <strong><?=count($interactions)?></strong>
    </div>
  </div>

  <?php if(empty($interactions)): ?>
    <div class="empty-state">
      <div class="empty-state-icon">
        <i class="fas fa-comment-slash"></i>
      </div>
      <div class="empty-state-title">No Interactions Yet</div>
      <div class="empty-state-text">Interactions will appear here once you start tracking communication with sponsors</div>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Sponsor</th>
            <th>Type</th>
            <th>Notes</th>
            <th>Follow-up</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($interactions as $it): ?>
            <tr>
              <td>
                <div style="font-weight: 600;"><?=date('M d, Y', strtotime($it['created_on']))?></div>
                <small style="color: var(--gray-500);"><?=date('g:i A', strtotime($it['created_on']))?></small>
              </td>
              <td>
                <a href="/view_sponsor.php?id=<?=$it['sponsor_id']?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                  <?=htmlspecialchars($it['company_name'])?>
                </a>
                <?php if($it['contact_person']): ?>
                  <br><small style="color: var(--gray-500);"><?=htmlspecialchars($it['contact_person'])?></small>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge badge-new"><?=htmlspecialchars($it['interaction_type'])?></span>
              </td>
              <td>
                <div style="max-width: 300px;">
                  <?=nl2br(htmlspecialchars(substr($it['notes'], 0, 100)))?><?=strlen($it['notes']) > 100 ? '...' : ''?>
                </div>
              </td>
              <td>
                <?php if($it['next_followup_date']): ?>
                  <div style="padding: 0.25rem 0.75rem; background: var(--warning); color: white; border-radius: var(--radius-sm); display: inline-block; font-size: 0.875rem;">
                    <i class="fas fa-calendar-check"></i> <?=date('M d, Y', strtotime($it['next_followup_date']))?>
                  </div>
                <?php else: ?>
                  <span style="color: var(--gray-400);">-</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="/view_sponsor.php?id=<?=$it['sponsor_id']?>" class="action-btn action-btn-view" data-tooltip="View Sponsor">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
