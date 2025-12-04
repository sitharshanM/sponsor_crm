<?php
// Test API directly
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'login';
file_put_contents('php://temp', '{"username":"admin","password":"admin123"}');
$_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

// Mock input stream
class MockInput {
    public static function getContents() {
        return '{"username":"admin","password":"admin123"}';
    }
}

// Override file_get_contents for php://input
function testLogin() {
    require __DIR__ . '/config/database.php';
    require __DIR__ . '/src/User.php';
    
    $userModel = new User($pdo);
    $data = json_decode('{"username":"admin","password":"admin123"}', true);
    $username = $data['username'];
    $password = $data['password'];
    
    $user = $userModel->findByUsername($username);
    if ($user && $userModel->verifyPassword($user, $password)) {
        echo "SUCCESS: Password verified\n";
        echo "User ID: " . $user['id'] . "\n";
        echo "Username: " . $user['username'] . "\n";
    } else {
        echo "FAILED: Invalid credentials\n";
    }
}

testLogin();

