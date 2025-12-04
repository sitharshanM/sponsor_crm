<?php
// Direct API test
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/User.php';

$userModel = new User($pdo);
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['username']) && isset($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];
    
    $user = $userModel->findByUsername($username);
    if ($user && $userModel->verifyPassword($user, $password)) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'] ?? ''
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

