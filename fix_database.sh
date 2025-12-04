#!/bin/bash
# fix_database.sh - Quick fix for database authentication

echo "=== Database Setup Fix ==="
echo ""
echo "This script will create a dedicated database user for Sponsor CRM."
echo "You'll need sudo access to run MySQL commands."
echo ""
read -p "Press Enter to continue or Ctrl+C to cancel..."

# Create SQL commands
SQL=$(cat <<EOF
CREATE DATABASE IF NOT EXISTS sponsor_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS 'sponsor_crm'@'localhost' IDENTIFIED BY 'sponsor_crm_pass';
GRANT ALL PRIVILEGES ON sponsor_crm.* TO 'sponsor_crm'@'localhost';
FLUSH PRIVILEGES;
EOF
)

echo "Running MySQL commands..."
echo "$SQL" | sudo mysql

if [ $? -eq 0 ]; then
    echo ""
    echo "✓ Database user created successfully!"
    echo ""
    echo "Now update config/database.php with:"
    echo "  \$DB_USER = 'sponsor_crm';"
    echo "  \$DB_PASS = 'sponsor_crm_pass';"
    echo ""
    echo "Then run: php setup.php"
else
    echo ""
    echo "✗ Failed to create database user."
    echo "Please run manually: sudo mysql < setup_sql.sql"
fi

