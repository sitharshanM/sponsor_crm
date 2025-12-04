<?php
// Direct API endpoint for auth
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/User.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['action'] ?? 'login';

$userModel = new User($pdo);

switch ($path) {
    case 'check':
        if ($method === 'GET') {
            if (isset($_SESSION['user_id'])) {
                $user = $userModel->find($_SESSION['user_id']);
                echo json_encode([
                    'authenticated' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'] ?? '',
                        'full_name' => $user['full_name'] ?? ''
                    ]
                ]);
            } else {
                echo json_encode(['authenticated' => false]);
            }
        }
        break;
        
    case 'login':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Username and password required']);
                break;
            }
            
            $user = $userModel->findByUsername($username);
            if ($user && $userModel->verifyPassword($user, $password)) {
                $_SESSION['user_id'] = $user['id'];
                $userModel->updateLastLogin($user['id']);
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'] ?? '',
                        'full_name' => $user['full_name'] ?? ''
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
            }
        }
        break;
        
    case 'logout':
        if ($method === 'POST') {
            session_destroy();
            echo json_encode(['success' => true]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
}

