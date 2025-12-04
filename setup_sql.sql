-- setup_sql.sql - Create database and user for Sponsor CRM
-- Run with: sudo mysql < setup_sql.sql

CREATE DATABASE IF NOT EXISTS sponsor_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Create a dedicated user for the application
CREATE USER IF NOT EXISTS 'sponsor_crm'@'localhost' IDENTIFIED BY 'sponsor_crm_pass';

-- Grant all privileges on the database
GRANT ALL PRIVILEGES ON sponsor_crm.* TO 'sponsor_crm'@'localhost';

FLUSH PRIVILEGES;

-- Now run: php migrations/create_database.php
-- Update config/database.php with:
-- $DB_USER = 'sponsor_crm';
-- $DB_PASS = 'sponsor_crm_pass';

