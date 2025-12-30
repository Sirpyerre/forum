#!/usr/bin/env bash
# exit on error
set -o errexit

echo "🚀 Starting build process..."

# Configure Flux credentials if available
if [ -n "$FLUX_USERNAME" ] && [ -n "$FLUX_LICENSE_KEY" ]; then
    echo "🔑 Configuring Flux credentials..."
    composer config http-basic.composer.fluxui.dev "$FLUX_USERNAME" "$FLUX_LICENSE_KEY"
fi

# Install PHP dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies
echo "📦 Installing npm dependencies..."
npm ci

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# Clear and cache config
echo "⚙️  Optimizing configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction

# Seed database if needed (only on first deploy or when needed)
if [ "$SEED_DATABASE" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --no-interaction
fi

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link || true

# Index searchable models
echo "🔍 Indexing searchable models..."
php artisan scout:import "App\Models\Discussion" || true

echo "✅ Build completed successfully!"
