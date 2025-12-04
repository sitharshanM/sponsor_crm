# React Frontend Setup Guide

## Quick Start

### 1. Install Node.js Dependencies

```bash
cd /home/malcom/sponsor_crm
npm install
```

### 2. Start Development Servers

**Terminal 1 - PHP API Server:**
```bash
php -S localhost:8000 -t public
```

**Terminal 2 - React Dev Server:**
```bash
npm run dev
```

### 3. Access the Application

- **React Dev Server**: http://localhost:3000 (with hot reload)
- **PHP Server**: http://localhost:8000 (serves API and built React app)

## Development Workflow

1. **React Dev Mode** (Recommended for development):
   - Run `npm run dev` - Opens on port 3000
   - Vite proxies `/api/*` to `localhost:8000`
   - Hot module replacement enabled
   - Fast refresh for instant updates

2. **Production Build**:
   ```bash
   npm run build
   ```
   - Builds React app to `public/dist/`
   - PHP server serves the built app
   - Access via `http://localhost:8000`

## Features

✅ **Supabase-Inspired Dark UI**
- Dark sidebar navigation
- Clean, modern design
- Smooth animations
- Responsive layout

✅ **React Router**
- Client-side routing
- Protected routes
- Navigation guards

✅ **API Integration**
- RESTful API endpoints
- Axios for HTTP requests
- Error handling
- Loading states

✅ **Components**
- Dashboard with stats
- Sponsor list with search
- Add/Edit forms
- View details page

## Project Structure

```
frontend/
├── components/        # Reusable components
│   ├── Layout.jsx    # Main layout with sidebar
│   ├── Sidebar.jsx   # Navigation sidebar
│   └── TopBar.jsx    # Top navigation bar
├── pages/            # Page components
│   ├── Login.jsx
│   ├── Dashboard.jsx
│   ├── Sponsors.jsx
│   └── ...
├── contexts/         # React contexts
│   └── AuthContext.jsx
├── services/         # API services
│   └── api.js
└── main.jsx          # Entry point

api/                  # PHP API endpoints
├── auth.php          # Authentication
├── sponsors.php      # Sponsors CRUD
└── index.php         # API router
```

## API Endpoints

- `GET /api/auth/check` - Check auth status
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/sponsors` - List all sponsors
- `GET /api/sponsors?id=:id` - Get sponsor
- `POST /api/sponsors` - Create sponsor
- `PUT /api/sponsors?id=:id` - Update sponsor
- `DELETE /api/sponsors?id=:id` - Delete sponsor

## Troubleshooting

### Port Already in Use
```bash
# Change Vite port in vite.config.js
server: { port: 3001 }
```

### API Not Working
- Check PHP server is running on port 8000
- Verify API routes in `api/index.php`
- Check browser console for errors

### Build Errors
```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

## Next Steps

1. Complete Interactions page
2. Add Excel import UI
3. Add delete functionality
4. Add pagination
5. Add filters and sorting

