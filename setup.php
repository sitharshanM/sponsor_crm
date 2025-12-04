<?php
// setup.php - One-click setup script
echo "=== Sponsor CRM Setup ===\n\n";

// Check database connection first
require_once __DIR__ . '/config/database.php';

try {
    // Test connection
    $pdo->query("SELECT 1");
    echo "✓ Database connection successful\n\n";
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
    echo "SOLUTION: You need to configure database access first.\n\n";
    echo "Option 1 - Create dedicated user (Recommended):\n";
    echo "  1. Run: sudo mysql < setup_sql.sql\n";
    echo "  2. Edit config/database.php:\n";
    echo "     \$DB_USER = 'sponsor_crm';\n";
    echo "     \$DB_PASS = 'sponsor_crm_pass';\n";
    echo "  3. Run this setup again: php setup.php\n\n";
    echo "Option 2 - Use root with password:\n";
    echo "  1. Run: sudo mysql\n";
    echo "  2. Execute: ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_password';\n";
    echo "  3. Execute: FLUSH PRIVILEGES;\n";
    echo "  4. Edit config/database.php with your root password\n";
    echo "  5. Run this setup again: php setup.php\n\n";
    exit(1);
}

// Step 1: Run database migration
echo "Step 1: Creating database and tables...\n";
try {
    require_once __DIR__ . '/migrations/create_database.php';
    echo "✓ Database and tables created\n\n";
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Seed demo data
echo "Step 2: Seeding demo data...\n";
try {
    require_once __DIR__ . '/migrations/seed_demo_data.php';
    echo "✓ Demo data seeded\n\n";
} catch (Exception $e) {
    echo "✗ Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "=== Setup Complete! ===\n\n";
echo "Default login credentials:\n";
echo "  Username: admin\n";
echo "  Password: admin123\n\n";
echo "To start the application:\n";
echo "  php -S localhost:8000 -t public\n\n";
echo "Then open: http://localhost:8000\n";

