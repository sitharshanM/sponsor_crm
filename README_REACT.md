# React Frontend Setup

This project now uses React for the frontend with a PHP API backend.

## Setup Instructions

### 1. Install Dependencies

```bash
cd /home/malcom/sponsor_crm
npm install
```

### 2. Development Mode

Run both PHP server and React dev server:

**Terminal 1 - PHP API Server:**
```bash
php -S localhost:8000 -t public
```

**Terminal 2 - React Dev Server:**
```bash
npm run dev
```

Then access: `http://localhost:3000`

### 3. Production Build

```bash
npm run build
```

This builds React to `public/dist/` which can be served by your PHP server.

### 4. Update PHP to Serve React

Update your `public/index.php` to serve the React app:

```php
<?php
// Serve React app
if (file_exists(__DIR__ . '/dist/index.html')) {
    readfile(__DIR__ . '/dist/index.html');
} else {
    // Fallback to old PHP pages
    require_once __DIR__ . '/../includes/auth.php';
    // ... rest of old code
}
```

## Project Structure

```
sponsor_crm/
├── frontend/          # React application
│   ├── components/    # React components
│   ├── pages/         # Page components
│   ├── contexts/      # React contexts (Auth)
│   ├── services/      # API services
│   └── main.jsx       # Entry point
├── api/               # PHP API endpoints
│   ├── auth.php       # Authentication API
│   ├── sponsors.php   # Sponsors API
│   └── index.php      # API router
└── public/            # Public files
    └── dist/          # Built React app (after build)
```

## Features

- ✅ React Router for navigation
- ✅ Supabase-inspired dark UI
- ✅ API-based architecture
- ✅ Authentication context
- ✅ Modern component structure
- ✅ Responsive design

## API Endpoints

- `GET /api/auth/check` - Check authentication
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/sponsors` - List sponsors
- `GET /api/sponsors/:id` - Get sponsor
- `POST /api/sponsors` - Create sponsor
- `PUT /api/sponsors/:id` - Update sponsor
- `DELETE /api/sponsors/:id` - Delete sponsor

