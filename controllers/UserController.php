<?php
// controllers/UserController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/User.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
    }

    public function authenticate($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'error' => 'Username and password are required.'];
        }

        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }

        if (!$this->userModel->verifyPassword($user, $password)) {
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }

        $this->userModel->updateLastLogin($user['id']);
        return ['success' => true, 'user' => $user];
    }

    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if username exists
        if ($this->userModel->findByUsername($data['username'])) {
            return ['success' => false, 'errors' => ['Username already exists.']];
        }

        // Check if email exists
        if ($this->userModel->findByEmail($data['email'])) {
            return ['success' => false, 'errors' => ['Email already exists.']];
        }

        $id = $this->userModel->create($data);
        return ['success' => true, 'id' => $id];
    }

    public function all() {
        return $this->userModel->all();
    }

    public function find($id) {
        return $this->userModel->find($id);
    }

    private function validate($data) {
        $errors = [];
        if (empty(trim($data['username'] ?? ''))) {
            $errors[] = "Username is required.";
        }
        if (empty(trim($data['email'] ?? ''))) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($data['password'] ?? '')) {
            $errors[] = "Password is required.";
        } elseif (strlen($data['password']) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }
        return $errors;
    }
}

