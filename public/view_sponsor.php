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
    echo "Sponsor not found";
    exit;
}

$interactions = $interactionModel->forSponsor($id);

include __DIR__ . '/../includes/header.php';
?>

<h2><?=htmlspecialchars($sponsor['company_name'])?></h2>
<p>
  <strong>Contact:</strong> <?=htmlspecialchars($sponsor['contact_person'])?><br/>
  <strong>Email:</strong> <?=htmlspecialchars($sponsor['email'])?><br/>
  <strong>Phone:</strong> <?=htmlspecialchars($sponsor['phone'])?><br/>
  <strong>Industry:</strong> <?=htmlspecialchars($sponsor['industry'])?><br/>
  <strong>Type:</strong> <?=htmlspecialchars($sponsor['sponsor_type'])?><br/>
  <strong>Status:</strong> <?=htmlspecialchars($sponsor['status'])?><br/>
  <strong>Added:</strong> <?=htmlspecialchars($sponsor['added_on'])?><br/>
</p>

<hr/>
<h3>Interactions</h3>
<?php if(empty($interactions)): ?>
  <p>No interactions yet.</p>
<?php else: ?>
  <?php foreach($interactions as $it): ?>
    <div style="border:1px solid #ddd; padding:8px; margin-bottom:8px;">
      <strong><?=htmlspecialchars($it['interaction_type'])?></strong> — <?=htmlspecialchars($it['created_on'])?><br/>
      <?=nl2br(htmlspecialchars($it['notes']))?><br/>
      <?php if($it['next_followup_date']): ?>
        <em>Follow-up on <?=htmlspecialchars($it['next_followup_date'])?></em>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<hr/>
<h3>Add Interaction</h3>
<form method="post" action="/add_interaction.php">
  <input type="hidden" name="sponsor_id" value="<?=htmlspecialchars($id)?>">
  <label>Type<br><input type="text" name="interaction_type" value=""></label><br><br>
  <label>Notes<br><textarea name="notes" rows="4" cols="50"></textarea></label><br><br>
  <label>Next follow-up date<br><input type="date" name="next_followup_date"></label><br><br>
  <button type="submit">Add Interaction</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
