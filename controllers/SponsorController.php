<?php
// controllers/SponsorController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';

class SponsorController {
    private $sponsorModel;
    
    public function __construct() {
        global $pdo;
        $this->sponsorModel = new Sponsor($pdo);
    }

    public function index() {
        $sponsors = $this->sponsorModel->all();
        return $sponsors;
    }

    public function show($id) {
        return $this->sponsorModel->find($id);
    }

    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $id = $this->sponsorModel->create($data);
        return ['success' => true, 'id' => $id];
    }

    public function update($id, $data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $result = $this->sponsorModel->update($id, $data);
        return ['success' => $result];
    }

    public function delete($id) {
        return $this->sponsorModel->delete($id);
    }

    private function validate($data) {
        $errors = [];
        if (empty(trim($data['company_name'] ?? ''))) {
            $errors[] = "Company name is required.";
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        return $errors;
    }
}

