<?php
// API Router
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$segments = array_values(array_filter(explode('/', $path)));

$resource = $segments[0] ?? 'sponsors';
$action = $segments[1] ?? null;

// Route to appropriate API file
switch ($resource) {
    case 'auth':
        $_GET['action'] = $action;
        require __DIR__ . '/auth.php';
        break;
    case 'sponsors':
        if ($action) {
            $_GET['id'] = $action;
        }
        require __DIR__ . '/sponsors.php';
        break;
    case 'interactions':
        require __DIR__ . '/interactions.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
}

