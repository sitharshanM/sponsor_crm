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
        $success = "Sponsor created. <a href='/view_sponsor.php?id={$id}'>View</a>";
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h2>Add Sponsor</h2>

<?php if($errors): ?>
  <div style="color:red;">
    <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
  </div>
<?php endif; ?>

<?php if($success): ?>
  <div style="color:green;"><?= $success ?></div>
<?php endif; ?>

<form method="post">
  <label>Company name<br><input type="text" name="company_name" value="<?=htmlspecialchars($_POST['company_name'] ?? '')?>" required></label><br><br>
  <label>Contact person<br><input type="text" name="contact_person" value="<?=htmlspecialchars($_POST['contact_person'] ?? '')?>"></label><br><br>
  <label>Email<br><input type="email" name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>"></label><br><br>
  <label>Phone<br><input type="text" name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>"></label><br><br>
  <label>Industry<br><input type="text" name="industry" value="<?=htmlspecialchars($_POST['industry'] ?? '')?>"></label><br><br>
  <label>Sponsor type<br><input type="text" name="sponsor_type" value="<?=htmlspecialchars($_POST['sponsor_type'] ?? '')?>"></label><br><br>
  <label>Status<br>
    <select name="status">
      <option value="new">New</option>
      <option value="interested">Interested</option>
      <option value="in_progress">In Progress</option>
      <option value="closed">Closed</option>
      <option value="rejected">Rejected</option>
    </select>
  </label><br><br>
  <button type="submit">Create Sponsor</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
