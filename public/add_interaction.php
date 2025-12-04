<?php
// public/add_interaction.php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Interaction.php';

$interactionModel = new Interaction($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'sponsor_id' => $_POST['sponsor_id'] ?? null,
        'interaction_type' => trim($_POST['interaction_type'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'next_followup_date' => $_POST['next_followup_date'] ?? null
    ];

    if (empty($data['sponsor_id'])) {
        die("Missing sponsor_id");
    }
    $interactionModel->create($data);
    header("Location: /view_sponsor.php?id=" . intval($data['sponsor_id']));
    exit;
}

header('Location: /index.php');
