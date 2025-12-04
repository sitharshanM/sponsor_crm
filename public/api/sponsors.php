<?php
// Direct API endpoint for sponsors
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Sponsor.php';

$method = $_SERVER['REQUEST_METHOD'];
$sponsorModel = new Sponsor($pdo);

// Get ID from query string or URL path
$id = $_GET['id'] ?? null;
if (!$id) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    $apiIndex = array_search('api', $pathParts);
    if ($apiIndex !== false && isset($pathParts[$apiIndex + 2])) {
        $id = $pathParts[$apiIndex + 2];
    }
}

switch ($method) {
    case 'GET':
        if ($id) {
            $sponsor = $sponsorModel->find($id);
            if ($sponsor) {
                echo json_encode($sponsor);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Sponsor not found']);
            }
        } else {
            $sponsors = $sponsorModel->all();
            echo json_encode($sponsors);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['company_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Company name is required']);
            break;
        }
        $id = $sponsorModel->create($data);
        echo json_encode(['success' => true, 'id' => $id]);
        break;
        
    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['company_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Company name is required']);
            break;
        }
        $result = $sponsorModel->update($id, $data);
        echo json_encode(['success' => $result]);
        break;
        
    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }
        $result = $sponsorModel->delete($id);
        echo json_encode(['success' => $result]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

