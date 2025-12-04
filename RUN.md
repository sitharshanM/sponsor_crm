# How to Run Sponsor CRM

## Quick Start

### 1. Configure Database

Edit `config/database.php` and set your MySQL credentials:

```php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'sponsor_crm';
$DB_USER = 'root';
$DB_PASS = ''; // Your MySQL password
```

### 2. Run Setup

Run the setup script to create the database and seed demo data:

```bash
php setup.php
```

This will:
- Create the database and tables
- Create a default admin user (username: `admin`, password: `admin123`)
- Add sample sponsors and interactions

### 3. Start the Web Server

**Option A: PHP Built-in Server (Recommended for Development)**

```bash
php -S localhost:8000 -t public
```

Then open your browser to: `http://localhost:8000`

**Option B: Using Apache/Nginx**

Point your web server's document root to the `public/` directory.

For Apache, add to your virtual host:
```apache
DocumentRoot /home/malcom/sponsor_crm/public
<Directory /home/malcom/sponsor_crm/public>
    AllowOverride All
    Require all granted
</Directory>
```

### 4. Login

- **URL**: http://localhost:8000
- **Username**: `admin`
- **Password**: `admin123`

## Manual Setup (Alternative)

If you prefer to run steps individually:

```bash
# 1. Create database and tables
php migrations/create_database.php

# 2. Seed demo data (optional)
php migrations/seed_demo_data.php

# 3. Start server
php -S localhost:8000 -t public
```

## Troubleshooting

### Database Connection Error

- Ensure MySQL is running: `sudo systemctl status mysql`
- Verify credentials in `config/database.php`
- Check if database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Port Already in Use

If port 8000 is busy, use a different port:
```bash
php -S localhost:8080 -t public
```

### Permission Issues

Ensure PHP has write permissions for sessions:
```bash
chmod 755 /var/lib/php/sessions  # or your session directory
```

## Production Deployment

1. **Disable error display** in `config/app.php`:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

2. **Enable HTTPS** and update session settings:
   ```php
   ini_set('session.cookie_secure', 1);
   ```

3. **Change default password** immediately after first login

4. **Configure proper web server** (Apache/Nginx) with document root pointing to `public/`

