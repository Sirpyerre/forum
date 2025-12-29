# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel 12 forum/discussion application using Livewire 3 with Volt for reactive components, Laravel Fortify for authentication, and Pest for testing. The application follows modern Laravel conventions with a focus on simplicity and maintainability.

## Tech Stack

- **Backend:** Laravel 12.x (PHP 8.2+)
- **Frontend:** Livewire 3 + Volt (functional components), Flux UI components, Tailwind CSS 4
- **Authentication:** Laravel Fortify (supports 2FA)
- **Database:** MySQL (configurable via .env)
- **Testing:** Pest PHP with Laravel plugin
- **Build Tool:** Vite 7
- **Code Style:** Laravel Pint

## Development Commands

### Initial Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Or use the composer script:
```bash
composer setup
```

### Development Server
Start all development services (server, queue, logs, vite):
```bash
composer dev
```

This runs concurrently:
- PHP development server on port 8000
- Queue worker
- Log viewer (Laravel Pail)
- Vite dev server

Alternatively, run services individually:
```bash
php artisan serve        # Development server
npm run dev             # Vite dev server
php artisan queue:work  # Queue worker
php artisan pail        # Log viewer
```

### Testing
```bash
composer test           # Run all tests
php artisan test        # Alternative syntax
php artisan test --filter=TestName  # Run specific test
```

Pest uses SQLite in-memory database for tests (configured in phpunit.xml).

### Code Quality
```bash
./vendor/bin/pint       # Fix code style issues
./vendor/bin/pint --test  # Check without fixing
```

### Database
```bash
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Drop all tables and re-run
php artisan migrate:fresh --seed  # Include seeders
php artisan db:seed          # Run seeders only
```

### Build for Production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Architecture

### Component Structure

**Livewire with Volt:** This project uses Volt for functional Livewire components. Volt components are single-file components located in `resources/views/livewire/` with inline PHP logic using the `@volt` directive.

Example Volt component pattern:
```php
<?php

use function Livewire\Volt\{state};

state(['count' => 0]);

$increment = fn () => $this->count++;

?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
```

**Class-based Livewire components** are located in `app/Livewire/` when needed for more complex logic.

### Routes

- `routes/web.php` - Main application routes
- `routes/console.php` - Artisan console commands

Volt routes are registered using `Volt::route()` syntax. Route model binding uses custom keys (e.g., `{channel:slug}`) for SEO-friendly URLs.

### Authentication

Uses Laravel Fortify with the following features:
- Registration and login
- Password reset
- Email verification
- Two-factor authentication
- Profile management

Auth views are Volt components in `resources/views/livewire/auth/` and settings in `resources/views/livewire/settings/`.

### Database Conventions

- Use anonymous migrations (no class names)
- Foreign keys should use `constrained()` with `cascadeOnDelete()` where appropriate
- Add indexes on frequently queried columns (slugs, foreign keys)
- Use Eloquent attribute casting v2 syntax

### Models and Relationships

Models are in `app/Models/`. When creating relationships for the forum features:

- User hasMany Discussions, Replies, Likes, Watchers
- Channel hasMany Discussions
- Discussion belongsTo User, Channel; hasMany Replies, Watchers
- Reply belongsTo User, Discussion; hasMany Likes

Use eager loading to prevent N+1 queries.

### Testing Strategy

Pest PHP with Laravel plugin is configured. Tests use:
- `RefreshDatabase` trait for Feature tests
- SQLite in-memory database
- Factory pattern for test data

Test structure:
- `tests/Feature/` - Feature/integration tests
- `tests/Unit/` - Unit tests
- `tests/Pest.php` - Global test configuration

### Frontend Assets

- Entry points: `resources/css/app.css` and `resources/js/app.js`
- Vite handles hot module replacement in development
- Tailwind CSS 4 with JIT compilation
- Use Flux components for UI elements (documentation at https://fluxui.dev)

### Queue and Jobs

Queue connection defaults to `database` (configured in .env). Queue jobs should be placed in `app/Jobs/`.

Process queued jobs:
```bash
php artisan queue:work
```

### Code Style Guidelines

- Follow PSR-12 coding standards (enforced by Pint)
- Use Laravel's latest features and conventions
- Prefer single-action controllers where appropriate
- Use route model binding with custom keys for SEO
- Implement authorization using Policies (in `app/Policies/`)

### Security Practices

- All forms include CSRF protection automatically
- Use Eloquent/Query Builder to prevent SQL injection
- Sanitize markdown content before rendering
- Implement authorization checks using Gates and Policies
- Rate limiting configured on authentication routes

### Performance Considerations

- Eager load relationships to avoid N+1 queries
- Cache configuration in production
- Use database queue driver (not sync) in production
- Queue notification emails
- Add database indexes on foreign keys and searchable fields

## Forum Application Features

This application is being built as a StackOverflow-style forum with the following planned features:

### Database Schema

#### Users Table
- id, name, email, password (nullable for OAuth)
- avatar (nullable), admin (boolean), points (integer, default 50)
- email_verified_at, remember_token, timestamps

#### Channels Table
- id, title, slug (unique), description (nullable), timestamps

#### Discussions Table
- id, user_id (FK), channel_id (FK), title, slug (unique)
- content (text), views (integer, default 0), timestamps

#### Replies Table
- id, user_id (FK), discussion_id (FK), content (text)
- best_answer (boolean, default false), timestamps

#### Likes Table
- id, user_id (FK), reply_id (FK), timestamps
- Unique constraint on (user_id, reply_id)

#### Watchers Table
- id, user_id (FK), discussion_id (FK), timestamps
- Unique constraint on (user_id, discussion_id)

### Planned Routes Structure

**Public:**
- GET / - Forum listing
- GET /discussions - All discussions
- GET /channel/{channel:slug} - Discussions by channel
- GET /discussion/{discussion:slug} - Single discussion view

**Authenticated:**
- Discussion CRUD: POST/PATCH/DELETE /discussions
- Reply CRUD: POST/PATCH/DELETE /replies
- POST /replies/{reply}/like - Toggle like
- POST /discussions/{discussion}/watch - Toggle watch
- POST /replies/{reply}/best-answer - Mark best answer

**Admin:**
- Resource /admin/channels - Channel management

### Authorization Policies

- **DiscussionPolicy:** update/delete (owner only), markBestAnswer (owner only)
- **ReplyPolicy:** update/delete (owner only)
- **ChannelPolicy:** manage (admin only)

### Points System

Award points for:
- Creating discussions (+5)
- Posting replies (+3)
- Receiving likes (+2)
- Best answer selected (+15)
- Best answer given (+5)

### Implementation Notes

- Use Laravel Observers for slug generation from titles
- Implement markdown support (consider `league/commonmark`)
- Best answer: only one per discussion, only discussion author can mark
- Watch system: notify users of new replies (use queued notifications)
- Use Livewire for interactive features (likes, watch toggles)
