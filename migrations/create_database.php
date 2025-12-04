<?php
// migrations/create_database.php
// Read config values without connecting (since DB might not exist yet)
$config_file = __DIR__ . '/../config/database.php';
if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);
    // Extract DB credentials from config file
    preg_match("/\\\$DB_HOST\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $host_match);
    preg_match("/\\\$DB_USER\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $user_match);
    preg_match("/\\\$DB_PASS\s*=\s*['\"]([^'\"]*)['\"]/", $config_content, $pass_match);
    preg_match("/\\\$DB_NAME\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $name_match);
    
    $DB_HOST = $host_match[1] ?? '127.0.0.1';
    $DB_USER = $user_match[1] ?? 'root';
    $DB_PASS = $pass_match[1] ?? '';
    $DB_NAME = $name_match[1] ?? 'sponsor_crm';
} else {
    $DB_HOST = '127.0.0.1';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_NAME = 'sponsor_crm';
}
$DB_CHAR = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};charset={$DB_CHAR}", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET {$DB_CHAR} COLLATE {$DB_CHAR}_general_ci");
    echo "Database `{$DB_NAME}` ensured.\n";

    // Connect to the database
    $pdo->exec("USE `{$DB_NAME}`");

    // Create sponsors table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS sponsors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(50),
        industry VARCHAR(100),
        sponsor_type VARCHAR(50),
        status ENUM('new','interested','in_progress','closed','rejected') DEFAULT 'new',
        added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_on TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
    ");
    echo "Table sponsors created.\n";

    // Create interactions table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS interactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sponsor_id INT NOT NULL,
        interaction_type VARCHAR(50),
        notes TEXT,
        next_followup_date DATE NULL,
        created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ");
    echo "Table interactions created.\n";

    // Create users table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        full_name VARCHAR(255),
        created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    ) ENGINE=InnoDB;
    ");
    echo "Table users created.\n";

    echo "Migration complete.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
