<?php
// public/edit_sponsor.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';

$sponsorModel = new Sponsor($pdo);
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /index.php');
    exit;
}
$sponsor = $sponsorModel->find($id);
if (!$sponsor) {
    die("Sponsor not found.");
}

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

    if ($data['company_name'] === '') $errors[] = "Company name required.";
    if (empty($errors)) {
        $sponsorModel->update($id, $data);
        $success = "Updated successfully.";
        $sponsor = $sponsorModel->find($id); // refresh
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h2>Edit Sponsor</h2>

<?php if($errors): ?>
  <div style="color:red;"><?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div>
<?php endif; ?>
<?php if($success): ?>
  <div style="color:green;"><?=htmlspecialchars($success)?></div>
<?php endif; ?>

<form method="post">
  <label>Company name<br><input type="text" name="company_name" value="<?=htmlspecialchars($sponsor['company_name'])?>" required></label><br><br>
  <label>Contact person<br><input type="text" name="contact_person" value="<?=htmlspecialchars($sponsor['contact_person'])?>"></label><br><br>
  <label>Email<br><input type="email" name="email" value="<?=htmlspecialchars($sponsor['email'])?>"></label><br><br>
  <label>Phone<br><input type="text" name="phone" value="<?=htmlspecialchars($sponsor['phone'])?>"></label><br><br>
  <label>Industry<br><input type="text" name="industry" value="<?=htmlspecialchars($sponsor['industry'])?>"></label><br><br>
  <label>Sponsor type<br><input type="text" name="sponsor_type" value="<?=htmlspecialchars($sponsor['sponsor_type'])?>"></label><br><br>
  <label>Status<br>
    <select name="status">
      <?php
        $statuses = ['new','interested','in_progress','closed','rejected'];
        foreach($statuses as $st) {
            $sel = ($sponsor['status'] === $st) ? 'selected' : '';
            echo "<option value=\"{$st}\" {$sel}>".ucfirst(str_replace('_',' ',$st))."</option>";
        }
      ?>
    </select>
  </label><br><br>
  <button type="submit">Save Changes</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
