# Deployment Guide - Render.com

This guide explains how to deploy the Convene application on Render.com using their free tier.

## 📋 Prerequisites

- [Render.com](https://render.com) account (free)
- [GitHub](https://github.com) account
- GitHub repository with the application code

## 🚀 Automatic Deployment with Blueprint

### Option 1: Deploy with render.yaml (Recommended)

1. **Connect your repository to Render**
   - Go to [Render Dashboard](https://dashboard.render.com)
   - Click "New +" → "Blueprint"
   - Connect your GitHub repository
   - Select the `forum` repository

2. **Render will automatically detect the `render.yaml` file**
   - Creates a Web Service (Convene app): `convene`
   - Creates a PostgreSQL database: `convene-db` (1GB free)
   - Automatically configures all environment variables

3. **Wait for deployment to finish** (5-10 minutes first time)

4. **Done!** Your app will be available at the URL provided by Render.

### Option 2: Manual Deployment

If you prefer manual configuration:

#### Step 1: Create Database

1. In Render Dashboard, click "New +" → "PostgreSQL"
2. Configure:
   - **Name:** `convene-db`
   - **Database:** `convene`
   - **Region:** Oregon (closest)
   - **Plan:** Free
3. Click "Create Database"
4. Save the credentials (you'll need them later)

#### Step 2: Create Web Service

1. Click "New +" → "Web Service"
2. Connect your GitHub repository
3. Configure:
   - **Name:** `convene`
   - **Region:** Oregon
   - **Branch:** `main`
   - **Runtime:** Docker
   - **Build Command:** `./render-build.sh`
   - **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Plan:** Free

#### Step 3: Configure Environment Variables

In the "Environment" section of the Web Service, add:

```env
APP_NAME=Convene
APP_ENV=production
APP_DEBUG=false
APP_KEY=                          # Auto-generated
APP_URL=https://convene.onrender.com

DB_CONNECTION=pgsql
DB_HOST=                          # From PostgreSQL you created
DB_PORT=5432
DB_DATABASE=convene
DB_USERNAME=                      # From PostgreSQL you created
DB_PASSWORD=                      # From PostgreSQL you created

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
SCOUT_DRIVER=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@convene.com
MAIL_FROM_NAME=Convene
```

4. Click "Create Web Service"

## 🔄 CI/CD with GitHub Actions

The project includes a CI/CD workflow that:

- ✅ Runs all tests
- ✅ Checks code with Pint
- ✅ Compiles frontend assets
- ✅ Notifies when ready to deploy

### Automatic Workflow

1. **On Pull Requests:** Runs tests and validations
2. **On Push to `main`:** Runs tests + triggers automatic deploy on Render

To see workflow status:
- Go to your repository on GitHub
- Click on "Actions" tab
- You'll see all workflows running

## 📊 Monitoring and Logs

### View Real-time Logs

1. Go to your Web Service in Render Dashboard
2. Click on "Logs" tab
3. Logs update automatically

### Metrics and Performance

1. "Metrics" tab shows:
   - CPU usage
   - Memory usage
   - Request count
   - Response times

## 🔧 Useful Commands

### Run Migrations Manually

If you need to run migrations after deployment:

1. Go to your Web Service → Shell
2. Execute:
   ```bash
   php artisan migrate --force
   ```

### Seed Database (First time)

To populate database with sample data:

1. Add environment variable: `SEED_DATABASE=true`
2. Re-deploy the application
3. After deployment, remove or change to `SEED_DATABASE=false`

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Re-index Search

```bash
php artisan scout:import "App\Models\Discussion"
```

## ⚠️ Free Tier Limitations

- **Web Service:**
  - Sleeps after 15 minutes of inactivity
  - First request after sleep takes ~30 seconds
  - 750 compute hours free/month

- **PostgreSQL:**
  - 1GB storage
  - Expires after 90 days (you can create a new database)
  - Backups not included

- **Bandwidth:**
  - 100GB/month free

## 🚨 Troubleshooting

### Error: "Application key not set"

```bash
# In Render Shell
php artisan key:generate --show
# Copy the key and add it to environment variables as APP_KEY
```

### Error: "No such file or directory (storage)"

```bash
# In Render Shell
php artisan storage:link
```

### Migration Errors

1. Verify database is connected
2. Check credentials in environment variables
3. Run migrations manually from Shell

### Build Fails

1. Review build logs in Render
2. Ensure `render-build.sh` has execution permissions:
   ```bash
   chmod +x render-build.sh
   git add render-build.sh
   git commit -m "Make build script executable"
   git push
   ```

## 📧 Configure Email (Optional)

To send real emails in production, you can use:

### Option 1: Resend (Recommended - Free)

1. Create account at [Resend.com](https://resend.com)
2. Generate API Key
3. Update environment variables:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.resend.com
   MAIL_PORT=587
   MAIL_USERNAME=resend
   MAIL_PASSWORD=your_api_key
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   ```

### Option 2: Mailtrap (For Testing)

1. Create account at [Mailtrap.io](https://mailtrap.io)
2. Use the SMTP credentials they provide

## 🔐 Production Security

- ✅ `APP_DEBUG=false` in production
- ✅ `APP_ENV=production`
- ✅ Use HTTPS (Render provides it automatically)
- ✅ Sensitive variables in Environment Variables (never in code)
- ✅ Rate limiting configured on authentication routes

## 📱 Custom Domain (Optional)

1. Go to your Web Service → Settings → Custom Domains
2. Click "Add Custom Domain"
3. Enter your domain (e.g., `forum.yourdomain.com`)
4. Follow instructions to configure DNS
5. Render provides free SSL/HTTPS with Let's Encrypt

## 🔄 Update the Application

Simply push to the `main` branch:

```bash
git add .
git commit -m "Update feature"
git push origin main
```

Render will detect the change and deploy automatically.

## 📞 Support

- [Render Documentation](https://render.com/docs)
- [Render Community](https://community.render.com)
- [Laravel Deployment Docs](https://laravel.com/docs/deployment)
