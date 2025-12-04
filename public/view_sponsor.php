<?php
// public/view_sponsor.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';
require_once __DIR__ . '/../src/Interaction.php';

$sponsorModel = new Sponsor($pdo);
$interactionModel = new Interaction($pdo);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /index.php');
    exit;
}
$sponsor = $sponsorModel->find($id);
if (!$sponsor) {
    header('Location: /index.php');
    exit;
}

$interactions = $interactionModel->forSponsor($id);

$pageTitle = htmlspecialchars($sponsor['company_name']);
include __DIR__ . '/../includes/header.php';

if (isset($_GET['success'])) {
    echo '<script>showNotification("Sponsor created successfully!", "success");</script>';
}
?>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">
      <i class="fas fa-building"></i> <?=htmlspecialchars($sponsor['company_name'])?>
    </h2>
    <div class="action-buttons">
      <a href="/edit_sponsor.php?id=<?=$id?>" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
      </a>
      <a href="/sponsors.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
    <div>
      <h3 style="margin-bottom: 1rem; color: var(--gray-700); font-size: 1.125rem;">
        <i class="fas fa-info-circle"></i> Company Information
      </h3>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Company Name</strong>
          <div style="font-size: 1.125rem; margin-top: 0.25rem;"><?=htmlspecialchars($sponsor['company_name'])?></div>
        </div>
        <?php if($sponsor['industry']): ?>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Industry</strong>
          <div style="margin-top: 0.25rem;">
            <span class="badge badge-new"><?=htmlspecialchars($sponsor['industry'])?></span>
          </div>
        </div>
        <?php endif; ?>
        <?php if($sponsor['sponsor_type']): ?>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Sponsor Type</strong>
          <div style="margin-top: 0.25rem;"><?=htmlspecialchars($sponsor['sponsor_type'])?></div>
        </div>
        <?php endif; ?>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Status</strong>
          <div style="margin-top: 0.25rem;">
            <span class="badge badge-<?=str_replace('_', '-', $sponsor['status'])?>">
              <?=ucfirst(str_replace('_', ' ', $sponsor['status']))?>
            </span>
          </div>
        </div>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Added On</strong>
          <div style="margin-top: 0.25rem;"><?=date('F d, Y', strtotime($sponsor['added_on']))?></div>
        </div>
      </div>
    </div>

    <div>
      <h3 style="margin-bottom: 1rem; color: var(--gray-700); font-size: 1.125rem;">
        <i class="fas fa-user"></i> Contact Information
      </h3>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <?php if($sponsor['contact_person']): ?>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Contact Person</strong>
          <div style="margin-top: 0.25rem;">
            <i class="fas fa-user"></i> <?=htmlspecialchars($sponsor['contact_person'])?>
          </div>
        </div>
        <?php endif; ?>
        <?php if($sponsor['email']): ?>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Email</strong>
          <div style="margin-top: 0.25rem;">
            <a href="mailto:<?=htmlspecialchars($sponsor['email'])?>" style="color: var(--primary); text-decoration: none;">
              <i class="fas fa-envelope"></i> <?=htmlspecialchars($sponsor['email'])?>
            </a>
          </div>
        </div>
        <?php endif; ?>
        <?php if($sponsor['phone']): ?>
        <div>
          <strong style="color: var(--gray-600); font-size: 0.875rem;">Phone</strong>
          <div style="margin-top: 0.25rem;">
            <a href="tel:<?=htmlspecialchars($sponsor['phone'])?>" style="color: var(--primary); text-decoration: none;">
              <i class="fas fa-phone"></i> <?=htmlspecialchars($sponsor['phone'])?>
            </a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">
      <i class="fas fa-comments"></i> Interactions (<?=count($interactions)?>)
    </h2>
  </div>

  <?php if(empty($interactions)): ?>
    <div class="empty-state" style="padding: 2rem;">
      <div class="empty-state-icon">
        <i class="fas fa-comment-slash"></i>
      </div>
      <div class="empty-state-title">No Interactions Yet</div>
      <div class="empty-state-text">Start tracking your communication with this sponsor</div>
    </div>
  <?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <?php foreach($interactions as $it): ?>
        <div style="padding: 1.5rem; background: var(--gray-100); border-radius: var(--radius); border-left: 4px solid var(--primary);">
          <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
            <div>
              <span class="badge badge-new" style="margin-bottom: 0.5rem; display: inline-block;">
                <?=htmlspecialchars($it['interaction_type'])?>
              </span>
              <div style="color: var(--gray-600); font-size: 0.875rem;">
                <i class="fas fa-calendar"></i> <?=date('F d, Y g:i A', strtotime($it['created_on']))?>
              </div>
            </div>
          </div>
          <?php if($it['notes']): ?>
            <p style="color: var(--gray-700); margin: 0 0 0.75rem 0; line-height: 1.6;">
              <?=nl2br(htmlspecialchars($it['notes']))?>
            </p>
          <?php endif; ?>
          <?php if($it['next_followup_date']): ?>
            <div style="padding: 0.75rem; background: var(--white); border-radius: var(--radius-sm); margin-top: 0.75rem;">
              <i class="fas fa-calendar-check" style="color: var(--warning);"></i>
              <strong>Follow-up:</strong> <?=date('F d, Y', strtotime($it['next_followup_date']))?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">
      <i class="fas fa-plus-circle"></i> Add New Interaction
    </h2>
  </div>
  <form method="post" action="/add_interaction.php">
    <input type="hidden" name="sponsor_id" value="<?=htmlspecialchars($id)?>">
    <div class="form-group">
      <label class="form-label">
        <i class="fas fa-tag"></i> Interaction Type
      </label>
      <input type="text" name="interaction_type" class="form-control" 
             placeholder="e.g., Phone Call, Email, Meeting" required>
    </div>
    <div class="form-group">
      <label class="form-label">
        <i class="fas fa-sticky-note"></i> Notes
      </label>
      <textarea name="notes" class="form-control" rows="5" 
                placeholder="Enter interaction details..."></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">
        <i class="fas fa-calendar-check"></i> Next Follow-up Date
      </label>
      <input type="date" name="next_followup_date" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save"></i> Add Interaction
    </button>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
