<?php
/**
 * CLI Excel Import Script
 * For large files (2GB+), use this command-line script
 * 
 * Usage: php import_excel.php /path/to/file.xlsx
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Sponsor.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

// Check command line arguments
if ($argc < 2) {
    echo "Usage: php import_excel.php <excel_file> [options]\n";
    echo "Options:\n";
    echo "  --skip-header    Skip first row (header row)\n";
    echo "  --batch-size=N   Process N rows at a time (default: 1000)\n";
    echo "  --start-row=N    Start from row N (default: 1)\n";
    echo "  --max-rows=N     Import maximum N rows\n";
    echo "\nExample: php import_excel.php data.xlsx --skip-header --batch-size=500\n";
    exit(1);
}

$filePath = $argv[1];
$skipHeader = in_array('--skip-header', $argv);
$batchSize = 1000;
$startRow = 1;
$maxRows = null;

// Parse options
foreach ($argv as $arg) {
    if (strpos($arg, '--batch-size=') === 0) {
        $batchSize = (int)substr($arg, 13);
    }
    if (strpos($arg, '--start-row=') === 0) {
        $startRow = (int)substr($arg, 12);
    }
    if (strpos($arg, '--max-rows=') === 0) {
        $maxRows = (int)substr($arg, 11);
    }
}

if (!file_exists($filePath)) {
    die("Error: File not found: $filePath\n");
}

echo "=== Excel Import Started ===\n";
echo "File: $filePath\n";
echo "Batch size: $batchSize\n";
echo "Skip header: " . ($skipHeader ? 'Yes' : 'No') . "\n";
echo "\n";

try {
    // Load spreadsheet with memory-efficient settings
    $reader = IOFactory::createReaderForFile($filePath);
    $reader->setReadDataOnly(true);
    $reader->setReadEmptyCells(false);
    
    $spreadsheet = $reader->load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    
    echo "Spreadsheet dimensions: $highestRow rows, up to column $highestColumn\n";
    
    // Detect column mapping from first row
    $headerRow = 1;
    $columnMap = [];
    $headerData = [];
    
    for ($col = 'A'; $col <= $highestColumn; $col++) {
        $cellValue = $worksheet->getCell($col . $headerRow)->getValue();
        $headerData[$col] = strtolower(trim($cellValue ?? ''));
    }
    
    // Auto-detect column mapping
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
        if (strpos($header, 'company') !== false || strpos($header, 'name') !== false) {
            $columnMap['company_name'] = $columnMap['company_name'] ?? $col;
        }
        if (strpos($header, 'contact') !== false || strpos($header, 'person') !== false) {
            $columnMap['contact_person'] = $columnMap['contact_person'] ?? $col;
        }
        if (strpos($header, 'email') !== false || strpos($header, 'e-mail') !== false) {
            $columnMap['email'] = $col;
        }
        if (strpos($header, 'phone') !== false || strpos($header, 'tel') !== false) {
            $columnMap['phone'] = $col;
        }
        if (strpos($header, 'industry') !== false) {
            $columnMap['industry'] = $col;
        }
        if (strpos($header, 'type') !== false || strpos($header, 'sponsor') !== false) {
            $columnMap['sponsor_type'] = $columnMap['sponsor_type'] ?? $col;
        }
        if (strpos($header, 'status') !== false) {
            $columnMap['status'] = $col;
        }
    }
    
    echo "Column mapping detected:\n";
    foreach ($columnMap as $field => $col) {
        echo "  $field: " . ($col ? $col . " (" . $headerData[$col] . ")" : "Not found") . "\n";
    }
    echo "\n";
    
    if (!$columnMap['company_name']) {
        die("Error: Could not detect 'company_name' column. Please check your Excel file headers.\n");
    }
    
    // Initialize model
    $sponsorModel = new Sponsor($pdo);
    
    // Process rows in batches
    $currentRow = $skipHeader ? 2 : $startRow;
    $endRow = $maxRows ? min($currentRow + $maxRows - 1, $highestRow) : $highestRow;
    $totalProcessed = 0;
    $totalSuccess = 0;
    $totalErrors = 0;
    $startTime = microtime(true);
    
    echo "Starting import from row $currentRow to row $endRow...\n\n";
    
    while ($currentRow <= $endRow) {
        $batch = [];
        $batchEnd = min($currentRow + $batchSize - 1, $endRow);
        
        for ($row = $currentRow; $row <= $batchEnd; $row++) {
            $data = [];
            foreach ($columnMap as $field => $col) {
                if ($col) {
                    $value = $worksheet->getCell($col . $row)->getValue();
                    $data[$field] = $value ? trim($value) : null;
                } else {
                    $data[$field] = null;
                }
            }
            
            // Skip empty rows
            if (empty($data['company_name'])) {
                continue;
            }
            
            $batch[] = $data;
        }
        
        if (!empty($batch)) {
            $result = $sponsorModel->createBatch($batch);
            $totalSuccess += $result['success'];
            $totalErrors += count($result['errors']);
            $totalProcessed += count($batch);
            
            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    echo "  Warning: $error\n";
                }
            }
        }
        
        $currentRow = $batchEnd + 1;
        
        // Progress update
        $progress = round(($currentRow - $startRow) / ($endRow - $startRow + 1) * 100, 1);
        echo "Progress: $progress% ($currentRow/$endRow rows) - Imported: $totalSuccess, Errors: $totalErrors\r";
    }
    
    $elapsed = round(microtime(true) - $startTime, 2);
    
    echo "\n\n=== Import Complete ===\n";
    echo "Total rows processed: $totalProcessed\n";
    echo "Successfully imported: $totalSuccess\n";
    echo "Errors: $totalErrors\n";
    echo "Time elapsed: {$elapsed}s\n";
    echo "Average: " . round($totalSuccess / max($elapsed, 1), 0) . " rows/second\n";
    
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    
} catch (ReaderException $e) {
    die("Error reading Excel file: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

