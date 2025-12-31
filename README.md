# 🗣️ Convene

A modern, feature-rich discussion forum built with Laravel 12, Livewire 3, and Tailwind CSS 4. A StackOverflow-style platform for creating discussions, posting replies, and building a knowledge-sharing community.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=flat&logo=livewire)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-4.x-38B2AC?style=flat&logo=tailwind-css)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Features

### 💬 Discussion System
- **Create & Manage Discussions** - Post questions and topics with Markdown support
- **Organized Channels** - Categorize discussions by topic
- **Rich Text Editor** - Markdown toolbar with live preview
- **View Tracking** - Automatic view counting for discussions
- **Watch System** - Follow discussions and get notified of new replies

### 📸 Image Upload System
- **Multi-Image Upload** - Attach up to 5 images per discussion, 3 per reply
- **Drag & Drop** - Intuitive drag-and-drop interface
- **Image Gallery** - Beautiful lightbox with keyboard navigation
- **Automatic Optimization** - Images resized and compressed automatically
- **Multi-Storage Support** - Local, S3, or Cloudinary (easily scalable)
- **Real-time Preview** - See images before uploading

### 💬 Reply System
- **Threaded Replies** - Respond to discussions with full Markdown support
- **Best Answer** - Discussion authors can mark the most helpful reply
- **Like System** - Upvote helpful replies
- **Image Attachments** - Add visual context to your answers

### 🏆 Gamification
- **Points System** - Earn points for participation
  - +5 points for creating discussions
  - +3 points for posting replies
  - +2 points for receiving likes
  - +15 points for best answer (recipient)
  - +5 points for selecting best answer (author)
- **Badge System** - Unlock achievements for milestones
- **Leaderboard** - See top contributors

### 🔐 Authentication & Security
- **Laravel Fortify** - Secure authentication system
- **Two-Factor Authentication** - Optional 2FA for enhanced security
- **Email Verification** - Verify user accounts
- **Password Reset** - Secure password recovery
- **Authorization Policies** - Role-based permissions
- **CSRF Protection** - All forms protected

### 🔍 Search & Discovery
- **Laravel Scout** - Fast full-text search
- **Search by Keyword** - Find discussions quickly
- **Filter by Channel** - Browse by category
- **Sorting Options** - Latest, popular, or unanswered

### 📧 Notifications
- **Email Notifications** - Get notified about:
  - New replies to watched discussions
  - Best answer selections
  - Welcome emails
- **Queued Jobs** - Email sending handled asynchronously
- **Customizable** - Choose notification preferences

### 🎨 Modern UI/UX
- **Tailwind CSS 4** - Beautiful, responsive design
- **Dark Mode** - Full dark mode support
- **Mobile-First** - Optimized for all screen sizes
- **Alpine.js** - Interactive components without page reloads
- **Loading States** - Visual feedback for all actions
- **Animations** - Smooth transitions and effects

### 🔧 Developer Experience
- **Laravel 12** - Latest Laravel features
- **Livewire 3 + Volt** - Reactive components with minimal JavaScript
- **Pest Testing** - 44 comprehensive tests with 107 assertions
- **Laravel Pint** - Consistent code formatting
- **GitHub Actions** - Automated CI/CD pipelines
- **Branching Strategy** - Development and production workflows

### 📊 SEO & Performance
- **Meta Tags** - Proper SEO meta tags on all pages
- **Open Graph** - Social media sharing optimization
- **Sitemap** - XML sitemap for search engines
- **Canonical URLs** - Prevent duplicate content
- **Lazy Loading** - Images loaded on demand
- **Asset Optimization** - Minified CSS/JS bundles

## 🚀 Tech Stack

**Backend:**
- Laravel 12.x
- PHP 8.4
- PostgreSQL / MySQL
- Laravel Fortify (Authentication)
- Laravel Scout (Search)

**Frontend:**
- Livewire 3 + Volt
- Alpine.js 3
- Tailwind CSS 4
- Vite 7

**Storage:**
- Local filesystem
- Amazon S3
- Cloudinary

**Testing:**
- Pest PHP
- Laravel Dusk (Browser testing)

**DevOps:**
- Docker
- GitHub Actions
- Render / Laravel Forge

## 📋 Requirements

- PHP 8.4 or higher
- Composer
- Node.js 18+ and npm
- PostgreSQL 13+ or MySQL 8+
- (Optional) Redis for caching/queues

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/convene.git
cd convene
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=convene
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Storage Configuration

```bash
# Create storage symlink
php artisan storage:link
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
# Using Laravel's built-in server
php artisan serve

# Or using Composer script (runs server + queue + logs + vite)
composer dev
```

Visit `http://localhost:8000` in your browser.

## 🧪 Testing

```bash
# Run all tests
composer test

# Run specific test file
php artisan test tests/Feature/DiscussionTest.php

# Run tests with coverage
php artisan test --coverage
```

**Test Coverage:**
- ✅ 44 tests
- ✅ 107 assertions
- ✅ Discussion & Reply CRUD
- ✅ Image upload system
- ✅ Authentication & Authorization
- ✅ Points & Badges system

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/      # HTTP Controllers
│   ├── Models/                # Eloquent Models
│   ├── Policies/              # Authorization Policies
│   ├── Services/              # Business Logic (ImageService)
│   └── Notifications/         # Email Notifications
├── database/
│   ├── migrations/            # Database Migrations
│   ├── factories/             # Model Factories
│   └── seeders/               # Database Seeders
├── resources/
│   ├── views/
│   │   ├── livewire/          # Volt Components
│   │   ├── discussions/       # Discussion Views
│   │   └── layouts/           # Layout Templates
│   ├── css/                   # Tailwind CSS
│   └── js/                    # JavaScript/Alpine.js
├── routes/
│   ├── web.php               # Web Routes
│   └── console.php           # Artisan Commands
├── tests/
│   ├── Feature/              # Feature Tests
│   └── Unit/                 # Unit Tests
└── docs/                     # Documentation
    ├── IMAGE_UPLOAD.md       # Image System Docs
    └── RENDER_DEPLOYMENT.md  # Deployment Guide
```

## 🖼️ Image Upload Configuration

The forum supports multiple storage drivers for images:

### Local Storage (Default)
```bash
IMAGES_DISK=public
```

### Cloudinary (Recommended for Production)
```bash
IMAGES_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

### Amazon S3
```bash
IMAGES_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=convene-images
```

For detailed image upload documentation, see [docs/IMAGE_UPLOAD.md](docs/IMAGE_UPLOAD.md).

## 🚀 Deployment

### Deploy to Render

This project includes deployment configuration for Render:

1. **Push to GitHub**
   ```bash
   git push origin main
   ```

2. **Deploy on Render**
   - Go to [Render Dashboard](https://dashboard.render.com/)
   - Click "New +" → "Blueprint"
   - Connect your repository
   - Render will auto-detect `render.yaml`

3. **Configure Environment Variables**
   - Set `APP_URL`, database credentials, storage, and mail settings
   - See [docs/RENDER_DEPLOYMENT.md](docs/RENDER_DEPLOYMENT.md) for details

For detailed deployment instructions, see [docs/RENDER_DEPLOYMENT.md](docs/RENDER_DEPLOYMENT.md).

### Deploy to Laravel Forge

1. Connect your server to Forge
2. Create a new site
3. Deploy from your Git repository
4. Configure environment variables
5. Enable queue worker

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Run Laravel Pint: `./vendor/bin/pint`
- Write tests for new features
- Update documentation as needed

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Livewire](https://livewire.laravel.com) - Reactive Components
- [Tailwind CSS](https://tailwindcss.com) - Utility-First CSS
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript
- [Pest](https://pestphp.com) - Testing Framework
- [Intervention Image](https://image.intervention.io) - Image Processing

## 📧 Support

For support, please open an issue on [GitHub Issues](https://github.com/yourusername/convene/issues).

## 🗺️ Roadmap

- [ ] Real-time notifications with WebSockets
- [ ] Advanced search with filters
- [ ] User profiles with activity history
- [ ] Private messaging system
- [ ] Moderation tools for admins
- [ ] API for mobile apps
- [ ] Multi-language support

---

Built with ❤️ using Laravel
