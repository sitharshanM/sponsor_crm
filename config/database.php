<?php
// config/database.php
// PDO connection - uses environment variables for production (Render, Railway, etc.)
// Falls back to local defaults for development
$DB_HOST = $_ENV['DB_HOST'] ?? '127.0.0.1';
$DB_NAME = $_ENV['DB_NAME'] ?? 'sponsor_crm';
$DB_USER = $_ENV['DB_USER'] ?? 'sponsor_crm';
$DB_PASS = $_ENV['DB_PASS'] ?? 'sponsor_crm_pass';
$DB_CHAR = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHAR}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // If DB doesn't exist, $pdo creation will fail. Migration script will create DB.
    die("Database connection failed: " . $e->getMessage());
}

