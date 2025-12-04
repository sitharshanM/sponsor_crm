<?php
// public/sponsors.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';

$sponsorModel = new Sponsor($pdo);
$sponsors = $sponsorModel->all();

include __DIR__ . '/../includes/header.php';
?>

<h2>All Sponsors</h2>

<?php if(empty($sponsors)): ?>
  <p>No sponsors yet. <a href="/add_sponsor.php">Add one</a>.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse;">
    <tr>
      <th>Company</th>
      <th>Contact</th>
      <th>Email</th>
      <th>Type</th>
      <th>Status</th>
      <th>Added</th>
      <th>Actions</th>
    </tr>
    <?php foreach($sponsors as $s): ?>
      <tr>
        <td><?=htmlspecialchars($s['company_name'])?></td>
        <td><?=htmlspecialchars($s['contact_person'])?></td>
        <td><?=htmlspecialchars($s['email'])?></td>
        <td><?=htmlspecialchars($s['sponsor_type'])?></td>
        <td><?=htmlspecialchars($s['status'])?></td>
        <td><?=htmlspecialchars($s['added_on'])?></td>
        <td>
          <a href="/view_sponsor.php?id=<?=$s['id']?>">View</a> |
          <a href="/edit_sponsor.php?id=<?=$s['id']?>">Edit</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

