<?php
// setup_helper.php - Helper to create database user and configure access
echo "=== Database Setup Helper ===\n\n";
echo "This script will help you set up database access.\n";
echo "You have two options:\n\n";
echo "Option 1: Create a dedicated database user (Recommended)\n";
echo "  Run: sudo mysql < setup_sql.sql\n\n";
echo "Option 2: Configure root user with password\n";
echo "  Run: sudo mysql\n";
echo "  Then execute:\n";
echo "    ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_password';\n";
echo "    FLUSH PRIVILEGES;\n\n";
echo "After setting up database access, update config/database.php with your credentials.\n";

