<?php
// Router for PHP built-in server
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Route API requests FIRST (before any file checks)
if (strpos($path, '/api/') === 0 || strpos($path, '/api') === 0) {
    // Extract the API path
    $apiPath = preg_replace('#^/api/?#', '', $path);
    $segments = array_values(array_filter(explode('/', $apiPath)));
    
    $resource = $segments[0] ?? 'sponsors';
    $action = $segments[1] ?? null;
    
    // Route to direct API files in public/api/
    if ($resource === 'auth' && file_exists(__DIR__ . '/api/auth.php')) {
        $_GET['action'] = $action ?: 'login';
        require __DIR__ . '/api/auth.php';
        exit;
    }
    
    // Fallback to api/ directory
    $_GET['action'] = $action;
    if ($action && $resource === 'sponsors') {
        $_GET['id'] = $action;
    }
    
    switch ($resource) {
        case 'auth':
            require __DIR__ . '/../api/auth.php';
            break;
        case 'sponsors':
            require __DIR__ . '/../api/sponsors.php';
            break;
        case 'interactions':
            require __DIR__ . '/../api/interactions.php';
            break;
        default:
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Resource not found']);
    }
    exit;
}

// Route everything else to index.php (for React)
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false; // Serve the file
}

// Serve React app or fallback
if (file_exists(__DIR__ . '/dist/index.html')) {
    readfile(__DIR__ . '/dist/index.html');
} else {
    // Fallback to old PHP pages
    if ($path === '/' || $path === '/index.php') {
        require_once __DIR__ . '/../includes/auth.php';
        requireAuth();
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../src/Sponsor.php';
        $sponsorModel = new Sponsor($GLOBALS['pdo']);
        $sponsors = $sponsorModel->all();
        include __DIR__ . '/../includes/header.php';
        echo '<h2>Recent Sponsors</h2>';
        // ... rest of old index.php
    } else {
        http_response_code(404);
        echo 'Not Found';
    }
}

