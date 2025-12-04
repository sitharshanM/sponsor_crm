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

include __DIR__ . '/../includes/header.php';
?>

<h2>All Interactions</h2>

<?php if(empty($interactions)): ?>
  <p>No interactions yet.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse;">
    <tr>
      <th>Date</th>
      <th>Sponsor</th>
      <th>Type</th>
      <th>Notes</th>
      <th>Follow-up</th>
      <th>Actions</th>
    </tr>
    <?php foreach($interactions as $it): ?>
      <tr>
        <td><?=htmlspecialchars($it['created_on'])?></td>
        <td>
          <a href="/view_sponsor.php?id=<?=$it['sponsor_id']?>">
            <?=htmlspecialchars($it['company_name'])?>
          </a>
        </td>
        <td><?=htmlspecialchars($it['interaction_type'])?></td>
        <td><?=nl2br(htmlspecialchars(substr($it['notes'], 0, 100)))?><?=strlen($it['notes']) > 100 ? '...' : ''?></td>
        <td><?=$it['next_followup_date'] ? htmlspecialchars($it['next_followup_date']) : '-'?></td>
        <td>
          <a href="/view_sponsor.php?id=<?=$it['sponsor_id']?>">View Sponsor</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

