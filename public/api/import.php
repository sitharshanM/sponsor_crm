<?php
// API endpoint for Excel import
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Sponsor.php';

// Check if PhpSpreadsheet is available
$phpspreadsheetAvailable = file_exists(__DIR__ . '/../../vendor/autoload.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$phpspreadsheetAvailable) {
            http_response_code(500);
            echo json_encode(['error' => 'PhpSpreadsheet library not installed. Please run: composer install']);
            exit;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            $errorMsg = 'No file uploaded';
            if (isset($_FILES['file']['error'])) {
                switch ($_FILES['file']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errorMsg = 'File is too large';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errorMsg = 'File upload was interrupted';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errorMsg = 'No file was selected';
                        break;
                }
            }
            echo json_encode(['error' => $errorMsg]);
            exit;
        }

        $file = $_FILES['file'];
        $allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/octet-stream'
        ];
        
        $isValidType = in_array($file['type'], $allowedTypes) || 
                      preg_match('/\.(xlsx|xls)$/i', $file['name']);

        if (!$isValidType) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type. Please upload .xlsx or .xls files only.']);
            exit;
        }

        require_once __DIR__ . '/../../vendor/autoload.php';
        
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . uniqid('import_') . '_' . basename($file['name']);
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save uploaded file']);
            exit;
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($uploadPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($uploadPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            
            // Detect headers
            $headerRow = 1;
            $headerData = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headerData[$col] = strtolower(trim($worksheet->getCell($col . $headerRow)->getValue() ?? ''));
            }
            
            // Map columns
            $columnMap = [
                'company_name' => null,
                'contact_person' => null,
                'email' => null,
                'phone' => null,
                'industry' => null,
                'sponsor_type' => null,
                'status' => null
            ];
            
            foreach ($headerData as $col => $header) {
                $header = strtolower($header);
                // Company name - prioritize "company" over just "name"
                if (strpos($header, 'company') !== false) {
                    $columnMap['company_name'] = $col;
                } elseif (strpos($header, 'name') !== false && !$columnMap['company_name']) {
                    $columnMap['company_name'] = $col;
                }
                // Contact person
                if (strpos($header, 'contact') !== false || strpos($header, 'person') !== false) {
                    $columnMap['contact_person'] = $columnMap['contact_person'] ?? $col;
                }
                // Email
                if (strpos($header, 'email') !== false || strpos($header, 'e-mail') !== false) {
                    $columnMap['email'] = $col;
                }
                // Phone
                if (strpos($header, 'phone') !== false || strpos($header, 'tel') !== false) {
                    $columnMap['phone'] = $col;
                }
                // Industry
                if (strpos($header, 'industry') !== false) {
                    $columnMap['industry'] = $col;
                }
                // Sponsor type - check for "type" or "sponsor type"
                if ((strpos($header, 'type') !== false && strpos($header, 'sponsor') !== false) ||
                    (strpos($header, 'sponsor') !== false && strpos($header, 'type') !== false)) {
                    $columnMap['sponsor_type'] = $columnMap['sponsor_type'] ?? $col;
                } elseif (strpos($header, 'type') !== false && !$columnMap['sponsor_type']) {
                    $columnMap['sponsor_type'] = $col;
                }
                // Status
                if (strpos($header, 'status') !== false) {
                    $columnMap['status'] = $col;
                }
            }
            
            if (!$columnMap['company_name']) {
                unlink($uploadPath);
                http_response_code(400);
                echo json_encode(['error' => 'Could not detect "Company Name" column in Excel file. Please check your headers.']);
                exit;
            }
            
            $sponsorModel = new Sponsor($pdo);
            $batch = [];
            $imported = 0;
            $skipped = 0;
            $errors = [];
            
            // Process rows (skip header, limit to 10000 for web)
            $maxRows = min($highestRow, 10001); // 1 header + 10000 rows
            for ($row = 2; $row <= $maxRows; $row++) {
                $data = [];
                foreach ($columnMap as $field => $col) {
                    $value = $col ? $worksheet->getCell($col . $row)->getValue() : null;
                    $data[$field] = $value ? trim((string)$value) : null;
                }
                
                if (empty($data['company_name'])) {
                    $skipped++;
                    continue;
                }
                
                $batch[] = $data;
                
                // Process batch when it reaches 500 rows
                if (count($batch) >= 500) {
                    $result = $sponsorModel->createBatch($batch);
                    $imported += $result['success'];
                    if (!empty($result['errors'])) {
                        $errors = array_merge($errors, $result['errors']);
                    }
                    $batch = [];
                }
            }
            
            // Process remaining batch
            if (!empty($batch)) {
                $result = $sponsorModel->createBatch($batch);
                $imported += $result['success'];
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
            }
            
            // Clean up
            unlink($uploadPath);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            echo json_encode([
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'total_rows' => $highestRow - 1,
                'errors' => array_slice($errors, 0, 10) // Limit errors shown
            ]);
            
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            if (file_exists($uploadPath)) unlink($uploadPath);
            http_response_code(400);
            echo json_encode(['error' => 'Error reading Excel file: ' . $e->getMessage()]);
        } catch (Exception $e) {
            if (file_exists($uploadPath)) unlink($uploadPath);
            http_response_code(500);
            echo json_encode(['error' => 'Import error: ' . $e->getMessage()]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
