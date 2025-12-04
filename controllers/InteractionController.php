<?php
// controllers/InteractionController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Interaction.php';

class InteractionController {
    private $interactionModel;
    
    public function __construct() {
        global $pdo;
        $this->interactionModel = new Interaction($pdo);
    }

    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $id = $this->interactionModel->create($data);
        return ['success' => true, 'id' => $id];
    }

    public function forSponsor($sponsorId) {
        return $this->interactionModel->forSponsor($sponsorId);
    }

    public function all() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT i.*, s.company_name 
            FROM interactions i 
            JOIN sponsors s ON i.sponsor_id = s.id 
            ORDER BY i.created_on DESC
        ");
        return $stmt->fetchAll();
    }

    private function validate($data) {
        $errors = [];
        if (empty($data['sponsor_id'])) {
            $errors[] = "Sponsor ID is required.";
        }
        if (empty(trim($data['interaction_type'] ?? ''))) {
            $errors[] = "Interaction type is required.";
        }
        return $errors;
    }
}

