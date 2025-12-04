<?php
// public/import_sponsors.php - Web-based Excel import
require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Sponsor.php';

// Check if PhpSpreadsheet is available
$phpspreadsheetAvailable = file_exists(__DIR__ . '/../vendor/autoload.php');

$errors = [];
$success = '';
$importStats = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $phpspreadsheetAvailable) {
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Please select a valid Excel file to upload.";
    } else {
        $file = $_FILES['excel_file'];
        $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                        'application/vnd.ms-excel',
                        'application/octet-stream'];
        
        if (!in_array($file['type'], $allowedTypes) && 
            !preg_match('/\.(xlsx|xls)$/i', $file['name'])) {
            $errors[] = "Invalid file type. Please upload .xlsx or .xls files only.";
        } else {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadPath = $uploadDir . basename($file['name']);
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
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
                    $columnMap = ['company_name' => null, 'contact_person' => null, 
                                 'email' => null, 'phone' => null, 
                                 'industry' => null, 'sponsor_type' => null, 'status' => null];
                    
                    foreach ($headerData as $col => $header) {
                        $header = strtolower($header);
                        if (strpos($header, 'company') !== false) $columnMap['company_name'] = $columnMap['company_name'] ?? $col;
                        if (strpos($header, 'contact') !== false) $columnMap['contact_person'] = $columnMap['contact_person'] ?? $col;
                        if (strpos($header, 'email') !== false) $columnMap['email'] = $col;
                        if (strpos($header, 'phone') !== false) $columnMap['phone'] = $col;
                        if (strpos($header, 'industry') !== false) $columnMap['industry'] = $col;
                        if (strpos($header, 'type') !== false) $columnMap['sponsor_type'] = $columnMap['sponsor_type'] ?? $col;
                        if (strpos($header, 'status') !== false) $columnMap['status'] = $col;
                    }
                    
                    if (!$columnMap['company_name']) {
                        $errors[] = "Could not detect 'company_name' column in Excel file.";
                    } else {
                        $sponsorModel = new Sponsor($pdo);
                        $batch = [];
                        $imported = 0;
                        $skipped = 0;
                        
                        // Process rows (skip header)
                        for ($row = 2; $row <= min($highestRow, 10000); $row++) { // Limit to 10k for web
                            $data = [];
                            foreach ($columnMap as $field => $col) {
                                $data[$field] = $col ? trim($worksheet->getCell($col . $row)->getValue() ?? '') : null;
                            }
                            
                            if (empty($data['company_name'])) {
                                $skipped++;
                                continue;
                            }
                            
                            $batch[] = $data;
                            
                            if (count($batch) >= 500) {
                                $result = $sponsorModel->createBatch($batch);
                                $imported += $result['success'];
                                $batch = [];
                            }
                        }
                        
                        if (!empty($batch)) {
                            $result = $sponsorModel->createBatch($batch);
                            $imported += $result['success'];
                        }
                        
                        $importStats = [
                            'imported' => $imported,
                            'skipped' => $skipped,
                            'total_rows' => $highestRow - 1
                        ];
                        
                        $success = "Successfully imported $imported sponsors.";
                        
                        // Clean up
                        unlink($uploadPath);
                        $spreadsheet->disconnectWorksheets();
                    }
                } catch (Exception $e) {
                    $errors[] = "Import error: " . $e->getMessage();
                    if (file_exists($uploadPath)) unlink($uploadPath);
                }
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h2>Import Sponsors from Excel</h2>

<?php if(!$phpspreadsheetAvailable): ?>
  <div style="background:#fff3cd; border:2px solid #ffc107; padding:15px; margin:15px 0; color:#856404;">
    <strong style="color:#856404;">PhpSpreadsheet not installed.</strong><br>
    <span style="color:#856404;">For large files (2GB+), install dependencies first:</span><br>
    <code style="background:#fff; padding:2px 6px; border:1px solid #ddd; color:#d63384;">composer install</code><br><br>
    <span style="color:#856404;">Then use the CLI import script for large files:</span><br>
    <code style="background:#fff; padding:2px 6px; border:1px solid #ddd; color:#d63384;">php import_excel.php /path/to/file.xlsx --skip-header</code>
  </div>
<?php endif; ?>

<?php if($errors): ?>
  <div style="background:#f8d7da; border:2px solid #dc3545; color:#721c24; padding:15px; margin:15px 0; border-radius:4px;">
    <strong style="color:#721c24;">Errors:</strong>
    <?php foreach($errors as $e) echo "<div style='color:#721c24; margin-top:5px;'>".htmlspecialchars($e)."</div>"; ?>
  </div>
<?php endif; ?>

<?php if($success): ?>
  <div style="background:#d1e7dd; border:2px solid #198754; color:#0f5132; padding:15px; margin:15px 0; border-radius:4px;">
    <strong style="color:#0f5132;">✓ Success:</strong> <span style="color:#0f5132;"><?=htmlspecialchars($success)?></span>
    <?php if($importStats): ?>
      <br><small style="color:#0f5132;">
        Total rows: <strong><?=$importStats['total_rows']?></strong>, 
        Imported: <strong><?=$importStats['imported']?></strong>, 
        Skipped: <strong><?=$importStats['skipped']?></strong>
      </small>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div style="background:#f8f9fa; padding:20px; margin:15px 0; border:2px solid #dee2e6; border-radius:4px;">
  <h3 style="color:#212529; margin-top:0;">Excel File Format</h3>
  <p style="color:#495057;">Your Excel file should have the following columns (headers in first row):</p>
  <ul style="color:#495057;">
    <li><strong style="color:#212529;">Company Name</strong> <span style="color:#6c757d;">(required)</span> - Column with company/organization name</li>
    <li><strong style="color:#212529;">Contact Person</strong> <span style="color:#6c757d;">(optional)</span> - Name of contact person</li>
    <li><strong style="color:#212529;">Email</strong> <span style="color:#6c757d;">(optional)</span> - Email address</li>
    <li><strong style="color:#212529;">Phone</strong> <span style="color:#6c757d;">(optional)</span> - Phone number</li>
    <li><strong style="color:#212529;">Industry</strong> <span style="color:#6c757d;">(optional)</span> - Industry sector</li>
    <li><strong style="color:#212529;">Type</strong> or <strong style="color:#212529;">Sponsor Type</strong> <span style="color:#6c757d;">(optional)</span> - Type of sponsor</li>
    <li><strong style="color:#212529;">Status</strong> <span style="color:#6c757d;">(optional)</span> - new, interested, in_progress, closed, rejected</li>
  </ul>
  <p style="color:#495057;"><strong style="color:#212529;">Note:</strong> For files larger than 100MB, use the CLI import script instead.</p>
</div>

<?php if($phpspreadsheetAvailable): ?>
<form method="post" enctype="multipart/form-data" style="max-width:600px; background:#ffffff; padding:20px; border:2px solid #0d6efd; border-radius:4px; margin:20px 0;">
  <label style="display:block; color:#212529; font-weight:bold; margin-bottom:8px;">
    Select Excel File (.xlsx or .xls)
  </label>
  <input type="file" name="excel_file" accept=".xlsx,.xls" required 
         style="width:100%; padding:8px; margin-bottom:15px; border:2px solid #ced4da; border-radius:4px; background:#fff; color:#212529;">
  <div>
    <button type="submit" style="background:#0d6efd; color:#fff; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
      Import Sponsors
    </button>
    <a href="/sponsors.php" style="margin-left:10px; color:#0d6efd; text-decoration:underline;">Cancel</a>
  </div>
</form>
<?php endif; ?>

<div style="margin-top:30px; padding:20px; background:#cfe2ff; border:2px solid #0d6efd; border-radius:4px;">
  <h3 style="color:#084298; margin-top:0;">For Large Files (2GB+)</h3>
  <p style="color:#084298;">Use the command-line import script for better performance:</p>
  <pre style="background:#ffffff; padding:15px; border:2px solid #0d6efd; border-radius:4px; color:#212529; overflow-x:auto;">php import_excel.php /path/to/file.xlsx --skip-header --batch-size=1000</pre>
  <p style="color:#084298;"><strong>Options:</strong></p>
  <ul style="color:#084298;">
    <li><code style="background:#fff; padding:2px 6px; border:1px solid #0d6efd; color:#d63384;">--skip-header</code> - Skip first row (header)</li>
    <li><code style="background:#fff; padding:2px 6px; border:1px solid #0d6efd; color:#d63384;">--batch-size=N</code> - Process N rows at a time (default: 1000)</li>
    <li><code style="background:#fff; padding:2px 6px; border:1px solid #0d6efd; color:#d63384;">--start-row=N</code> - Start from row N</li>
    <li><code style="background:#fff; padding:2px 6px; border:1px solid #0d6efd; color:#d63384;">--max-rows=N</code> - Import maximum N rows</li>
  </ul>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

