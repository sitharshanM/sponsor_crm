<?php
// public/add_sponsor.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';

$sponsorModel = new Sponsor($pdo);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'company_name' => trim($_POST['company_name'] ?? ''),
        'contact_person' => trim($_POST['contact_person'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'industry' => trim($_POST['industry'] ?? ''),
        'sponsor_type' => trim($_POST['sponsor_type'] ?? ''),
        'status' => $_POST['status'] ?? 'new'
    ];

    if ($data['company_name'] === '') {
        $errors[] = "Company name is required.";
    }

    if (empty($errors)) {
        $id = $sponsorModel->create($data);
        $success = "Sponsor created successfully!";
        header("Location: /view_sponsor.php?id={$id}&success=1");
        exit;
    }
}

$pageTitle = 'Add Sponsor';
include __DIR__ . '/../includes/header.php';
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
  <div class="card-header">
    <h2 class="card-title">
      <i class="fas fa-plus-circle"></i> Add New Sponsor
    </h2>
    <a href="/sponsors.php" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>

  <?php if($errors): ?>
    <div class="alert alert-error">
      <i class="fas fa-exclamation-circle"></i>
      <div>
        <?php foreach($errors as $e): ?>
          <div><?=htmlspecialchars($e)?></div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <form method="post" data-validate>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-building"></i> Company Name <span style="color: var(--danger);">*</span>
        </label>
        <input type="text" name="company_name" class="form-control" 
               value="<?=htmlspecialchars($_POST['company_name'] ?? '')?>" 
               required 
               placeholder="Enter company name">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-user"></i> Contact Person
        </label>
        <input type="text" name="contact_person" class="form-control" 
               value="<?=htmlspecialchars($_POST['contact_person'] ?? '')?>" 
               placeholder="Contact person name">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-envelope"></i> Email
        </label>
        <input type="email" name="email" class="form-control" 
               value="<?=htmlspecialchars($_POST['email'] ?? '')?>" 
               placeholder="email@example.com">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-phone"></i> Phone
        </label>
        <input type="text" name="phone" class="form-control" 
               value="<?=htmlspecialchars($_POST['phone'] ?? '')?>" 
               placeholder="+1 (555) 123-4567">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-industry"></i> Industry
        </label>
        <input type="text" name="industry" class="form-control" 
               value="<?=htmlspecialchars($_POST['industry'] ?? '')?>" 
               placeholder="e.g., Technology, Finance">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-tag"></i> Sponsor Type
        </label>
        <input type="text" name="sponsor_type" class="form-control" 
               value="<?=htmlspecialchars($_POST['sponsor_type'] ?? '')?>" 
               placeholder="e.g., Gold, Silver, Bronze">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">
        <i class="fas fa-info-circle"></i> Status
      </label>
      <select name="status" class="form-control">
        <option value="new" <?=($_POST['status'] ?? 'new') == 'new' ? 'selected' : ''?>>New</option>
        <option value="interested" <?=($_POST['status'] ?? '') == 'interested' ? 'selected' : ''?>>Interested</option>
        <option value="in_progress" <?=($_POST['status'] ?? '') == 'in_progress' ? 'selected' : ''?>>In Progress</option>
        <option value="closed" <?=($_POST['status'] ?? '') == 'closed' ? 'selected' : ''?>>Closed</option>
        <option value="rejected" <?=($_POST['status'] ?? '') == 'rejected' ? 'selected' : ''?>>Rejected</option>
      </select>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save"></i> Create Sponsor
      </button>
      <a href="/sponsors.php" class="btn btn-secondary btn-lg">
        <i class="fas fa-times"></i> Cancel
      </a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
