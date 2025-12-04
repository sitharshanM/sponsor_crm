# Sponsor CRM

A minimal, clean PHP-based Customer Relationship Management system for managing sponsors and interactions.

## Features

- **Sponsor Management**: Create, view, edit sponsors with company details, contact information, and status tracking
- **Interaction Tracking**: Log interactions with sponsors including notes and follow-up dates
- **User Authentication**: Secure login system with session management
- **Clean Architecture**: MVC-inspired structure with models, controllers, and views

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) with PHP support

## Installation

1. **Clone or download the project**

2. **Configure database** in `config/database.php`:
   ```php
   $DB_HOST = '127.0.0.1';
   $DB_NAME = 'sponsor_crm';
   $DB_USER = 'root';
   $DB_PASS = ''; // Set your password
   ```

3. **Run database migration**:
   ```bash
   php migrations/create_database.php
   ```

4. **Seed demo data** (optional):
   ```bash
   php migrations/seed_demo_data.php
   ```
   This creates:
   - Default admin user: `admin` / `admin123`
   - Sample sponsors and interactions

5. **Configure web server**:
   - Point document root to the `public/` directory
   - Or use PHP built-in server: `php -S localhost:8000 -t public`

6. **Access the application**:
   - Navigate to `http://localhost:8000` (or your configured URL)
   - Login with default credentials: `admin` / `admin123`

## Project Structure

```
sponsor_crm/
├── assets/          # CSS, JS, images
├── config/          # Configuration files (database, app)
├── controllers/     # Controller classes
├── includes/        # Shared includes (header, footer, auth)
├── migrations/      # Database migrations and seeds
├── public/          # Public-facing PHP pages
└── src/             # Model classes (Sponsor, Interaction, User)
```

## Default Credentials

After seeding demo data:
- **Username**: `admin`
- **Password**: `admin123`

**Important**: Change the default password in production!

## Database Schema

### Users Table
- User authentication and management

### Sponsors Table
- Company information, contact details, status tracking
- Status values: `new`, `interested`, `in_progress`, `closed`, `rejected`

### Interactions Table
- Interaction logs linked to sponsors
- Tracks interaction type, notes, and follow-up dates

## Security Notes

- Passwords are hashed using PHP's `password_hash()` with PASSWORD_DEFAULT
- Sessions use HTTP-only cookies
- Input validation and output escaping (htmlspecialchars) implemented
- SQL injection protection via PDO prepared statements

## Development

- Error reporting is enabled in `config/app.php` (disable in production)
- All pages require authentication except login page
- Controllers provide validation and business logic separation

## License

Built for internal use. Modify as needed.

