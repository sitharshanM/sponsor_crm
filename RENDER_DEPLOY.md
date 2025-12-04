# Deploy Sponsor CRM to Render

Step-by-step guide to deploy your Sponsor CRM application to Render.

---

## Step 1: Prepare Your Code

✅ The code is already prepared! The database config uses environment variables.

---

## Step 2: Push to GitHub

Make sure your code is on GitHub:

```bash
# If not already a git repo
git init
git add .
git commit -m "Initial commit"

# Create repo on GitHub, then:
git remote add origin https://github.com/yourusername/sponsor_crm.git
git push -u origin main
```

---

## Step 3: Sign Up for Render

1. Go to https://render.com
2. Click "Get Started for Free"
3. Sign up with GitHub (recommended) or email

---

## Step 4: Create PostgreSQL Database

1. In Render dashboard, click **"New +"**
2. Select **"PostgreSQL"**
3. Configure:
   - **Name**: `sponsor-crm-db`
   - **Database**: `sponsor_crm`
   - **User**: `sponsor_crm`
   - **Region**: Choose closest to you
   - **Plan**: Free (or Starter for production)
4. Click **"Create Database"**
5. **Wait for it to provision** (takes 1-2 minutes)

---

## Step 5: Get Database Connection Details

1. Click on your database
2. Go to **"Connections"** tab
3. Copy these values:
   - **Hostname** (Internal Database URL)
   - **Database Name**
   - **Username**
   - **Password** (click "Show" to reveal)

**Note**: Render provides PostgreSQL, but your app uses MySQL. We'll need to either:
- **Option A**: Use PostgreSQL (requires code changes)
- **Option B**: Use external MySQL (PlanetScale, Railway, etc.)

For now, let's use **Option B** - External MySQL.

---

## Step 6: Set Up External MySQL (Recommended)

### Option 6A: Use PlanetScale (Free MySQL)

1. Go to https://planetscale.com
2. Sign up (free)
3. Create database: `sponsor_crm`
4. Get connection details:
   - Host
   - Username
   - Password
   - Database name

### Option 6B: Use Railway MySQL (Free)

1. Go to https://railway.app
2. Sign up
3. New Project → Add MySQL
4. Get connection details

---

## Step 7: Create Web Service

1. In Render dashboard, click **"New +"**
2. Select **"Web Service"**
3. Connect your GitHub repository
4. Select your `sponsor_crm` repo
5. Configure:
   - **Name**: `sponsor-crm`
   - **Region**: Same as database
   - **Branch**: `main` (or your branch)
   - **Root Directory**: (leave empty)
   - **Environment**: `PHP`
   - **Build Command**: (leave empty)
   - **Start Command**: `php -S 0.0.0.0:$PORT -t public`

---

## Step 8: Set Environment Variables

In your Web Service settings, go to **"Environment"** tab and add:

```
DB_HOST=your-mysql-host
DB_NAME=sponsor_crm
DB_USER=your-mysql-user
DB_PASS=your-mysql-password
```

**For PlanetScale example:**
```
DB_HOST=aws.connect.psdb.cloud
DB_NAME=sponsor_crm
DB_USER=your-username
DB_PASS=your-password
```

**For Railway MySQL example:**
```
DB_HOST=containers-us-west-xxx.railway.app
DB_NAME=railway
DB_USER=root
DB_PASS=your-password
```

---

## Step 9: Deploy

1. Click **"Create Web Service"**
2. Render will start building
3. Wait for deployment (2-3 minutes)
4. Your app will be live at: `https://sponsor-crm.onrender.com`

---

## Step 10: Run Database Migration

After deployment, you need to create the database tables:

### Option A: Via Render Shell

1. Go to your Web Service
2. Click **"Shell"** tab
3. Run:
   ```bash
   php migrations/create_database.php
   ```

### Option B: Via SSH/One-off

1. In Render, create a **"Background Worker"**
2. Command: `php migrations/create_database.php`
3. Run it once, then delete the worker

### Option C: Manual SQL

Connect to your MySQL database and run the SQL from `migrations/create_database.php`

---

## Step 11: Seed Demo Data (Optional)

If you want demo data:

1. In Render Shell or via one-off worker:
   ```bash
   php migrations/seed_demo_data.php
   ```

---

## Step 12: Access Your App

1. Go to your Render dashboard
2. Click on your Web Service
3. Your app URL: `https://sponsor-crm.onrender.com`
4. Login with:
   - Username: `admin`
   - Password: `admin123`

---

## Troubleshooting

### Database Connection Error

- Check environment variables are set correctly
- Verify database is running
- Check firewall/network settings

### "Page Not Found"

- Verify `Start Command` is: `php -S 0.0.0.0:$PORT -t public`
- Check root directory is correct

### Import Not Working

- Large Excel imports may timeout on free tier
- Use CLI import script on your local machine
- Or upgrade to paid plan for longer execution times

### Free Tier Limitations

- **Spins down after 15 minutes** of inactivity
- First request after spin-down takes ~30 seconds
- Upgrade to Starter plan ($7/month) to avoid spin-down

---

## Using render.yaml (Alternative Method)

If you prefer, you can use the `render.yaml` file:

1. Push `render.yaml` to your repo
2. In Render, click **"New +"** → **"Blueprint"**
3. Connect your repo
4. Render will auto-detect and configure everything

---

## Post-Deployment Checklist

- [ ] Database migration completed
- [ ] Can access login page
- [ ] Can login with admin/admin123
- [ ] Can view sponsors
- [ ] Can add new sponsor
- [ ] Change default password!
- [ ] Set up custom domain (optional)
- [ ] Enable HTTPS (automatic on Render)

---

## Custom Domain

1. Go to Web Service → Settings
2. Click **"Custom Domains"**
3. Add your domain
4. Update DNS records as instructed

---

## Monitoring

- View logs: Web Service → Logs
- View metrics: Web Service → Metrics
- Set up alerts: Settings → Notifications

---

## Cost

- **Free Tier**: 
  - Web service spins down after inactivity
  - PostgreSQL included (free)
  - 750 hours/month
  
- **Starter Plan**: $7/month
  - Always on
  - No spin-down
  - Better performance

---

## Need Help?

- Render Docs: https://render.com/docs
- Render Community: https://community.render.com

---

**Your app will be live at**: `https://your-service-name.onrender.com`

Good luck with your deployment! 🚀

