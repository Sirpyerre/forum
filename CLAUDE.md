## Overview
An advanced forum/discussion application similar to StackOverflow, built with Laravel. This document provides detailed specifications to reproduce this application using modern Laravel versions.


## 🛠 Tech Stack
- **Backend:** Laravel 12.x (PHP 8.4+)
- **Database:** MySQL (Relational Schema)
- **Frontend:** Blade Templates + SCSS + Vanilla JS/Alpine.js
- **Deployment:** Render.com (Live Demo)

## Core Features

1. User Authentication & Authorization
- Traditional registration and login
- Social authentication (Google, GitHub, Facebook)
- User profiles with avatars
- Admin role system
- Points/reputation system

2. Discussion Management
- Create discussions with title and content
- Edit own discussions
- Delete own discussions
- Slug-based URLs for SEO
- Channel/category organization
- Markdown support in content

3. Reply System
- Post replies to discussions
- Edit own replies
- Delete own replies
- Markdown editor for formatting
- Best answer marking (by discussion author)
- Nested reply threading (optional enhancement)

4. Social Interactions
- Like/unlike replies
- Watch/unwatch discussions (notifications)
- User reputation points
- Activity tracking

5. Channel Management
- CRUD operations for channels
- Browse discussions by channel
- Channel descriptions and icons

## Database Schema

### Users Table
- id (primary key)
- name (string)
- email (string, unique)
- password (string, nullable for OAuth users)
- avatar (string, nullable)
- admin (boolean, default false)
- points (integer, default 50)
- email_verified_at (timestamp, nullable)
- remember_token (string, nullable)
- timestamps

### Channels Table
- id (primary key)
- title (string)
- slug (string, unique)
- description (text, nullable)
- timestamps

### Discussions Table
- id (primary key)
- user_id (foreign key -> users)
- channel_id (foreign key -> channels)
- title (string)
- slug (string, unique)
- content (text)
- views (integer, default 0)
- timestamps

### Replies Table
- id (primary key)
- user_id (foreign key -> users)
- discussion_id (foreign key -> discussions)
- content (text)
- best_answer (boolean, default false)
- timestamps

### Likes Table
- id (primary key)
- user_id (foreign key -> users)
- reply_id (foreign key -> replies)
- timestamps
- Unique constraint on (user_id, reply_id)

### Watchers Table
- id (primary key)
- user_id (foreign key -> users)
- discussion_id (foreign key -> discussions)
- timestamps
- Unique constraint on (user_id, discussion_id)

### Social Identities Table
- id (primary key)
- user_id (foreign key -> users)
- provider (string) - e.g., 'google', 'github'
- provider_user_id (string)
- access_token (text)
- refresh_token (text, nullable)
- timestamps
- Unique constraint on (provider, provider_user_id)

## Implementation Steps

### Phase 1: Project Setup
1. Install Laravel 12: `composer create-project laravel/laravel forum`
2. Configure database in .env file
3. Install Laravel Breeze: `composer require laravel/breeze --dev`
4. Setup authentication: `php artisan breeze:install`
5. Install Socialite: `composer require laravel/socialite`
6. Install Markdown parser: `composer require league/commonmark`

### Phase 2: Database Structure
1. Create migrations for all tables with foreign keys and constraints
2. Add indexes on frequently queried columns (slug, email)
3. Create model factories for testing
4. Create seeders for channels and test users
5. Run migrations: `php artisan migrate --seed`

### Phase 3: Models and Relationships
1. Create Eloquent models: Channel, Discussion, Reply, Like, Watcher
2. Define relationships:
   - User hasMany Discussions, Replies, Likes, Watchers
   - Channel hasMany Discussions
   - Discussion belongsTo User, Channel; hasMany Replies, Watchers
   - Reply belongsTo User, Discussion; hasMany Likes
   - Like belongsTo User, Reply
   - Watcher belongsTo User, Discussion
3. Add attribute casting (timestamps, booleans)
4. Implement slug generation using Laravel's Str::slug() in observers or mutators

### Phase 4: Routes and Controllers

#### Routes (web.php)
// Public routes 
* GET / - Home/Forum listing 
* GET /discussions - All discussions 
* GET /channel/{channel:slug} - Discussions by channel 
* GET /discussion/{discussion:slug} - Single discussion view

// Authenticated routes 
* POST /discussions - Create discussion 
* GET /discussions/create - Create form 
* PATCH /discussions/{discussion} - Update discussion 
* DELETE /discussions/{discussion} - Delete discussion 
* POST /discussions/{discussion}/replies - Create reply 
* PATCH /replies/{reply} - Update reply 
* DELETE /replies/{reply} - Delete reply 
* POST /replies/{reply}/like - Like/unlike reply 
* POST /discussions/{discussion}/watch - Watch/unwatch discussion 
* POST /replies/{reply}/best-answer - Mark as best answer
// Admin routes 
* Resource /admin/channels - Channel CRUD

// OAuth routes 
* GET /auth/{provider} - Redirect to provider 
* GET /auth/{provider}/callback - Handle provider callback

#### Controllers
- ForumController - Discussion listing, filtering
- DiscussionController - CRUD operations
- ReplyController - Reply CRUD, like/unlike, best answer
- ChannelController - Admin CRUD
- WatcherController - Watch/unwatch functionality
- SocialAuthController - OAuth handling

### Phase 5: Authorization Policies
Create policies for:
- DiscussionPolicy: update, delete (owner only), markBestAnswer (owner only)
- ReplyPolicy: update, delete (owner only)
- ChannelPolicy: manage (admin only)

### Phase 6: Views and Frontend
1. Create layouts with Blade components
2. Discussion listing with pagination
3. Single discussion view with replies
4. Markdown editor integration (e.g., EasyMDE, SimpleMDE)
5. Real-time like counter (Alpine.js or Livewire)
6. Responsive design with Tailwind CSS

### Phase 7: Features Implementation

#### Slug Generation
Use Laravel Observers or model events to auto-generate slugs from titles

#### Markdown Rendering
Create a custom Blade directive or helper function to parse markdown:
```php
use League\CommonMark\CommonMarkConverter;

function markdown($text) {
    $converter = new CommonMarkConverter();
    return $converter->convert($text);
}
````

### **Like System**

Toggle-based like/unlike functionality with optimistic UI updates

### **Watch System**

* Send notifications when new replies are posted
* Use Laravel Notifications or queued jobs
* Email digest option

### **Points System**

Award points for:

* Creating discussions (+5)
* Posting replies (+3)
* Receiving likes (+2)
* Best answer selected (+15)
* Best answer given (+5)

### **Best Answer**

* Only discussion author can mark best answer
* Only one best answer per discussion
* Highlight visually in UI

## **Phase 8: Advanced Features**

### **Search Functionality**

* Laravel Scout with Meilisearch/Algolia
* Search discussions by title, content, author
* Filter by channel, date range

### **Spam Prevention**

* Rate limiting on posting
* CAPTCHA on registration
* Content moderation flags

### **User Profiles**

* View user's discussions and replies
* Display reputation points
* Edit profile and avatar

### **Notifications**

* Email notifications for watched discussions
* In-app notification center
* Configurable notification preferences

### **Admin Dashboard**

* User management
* Content moderation
* Channel management
* Analytics and statistics

### **Testing Strategy**

* Feature tests for all CRUD operations
* Policy tests for authorization
* Unit tests for business logic (points calculation, best answer)
* Browser tests with Laravel Dusk for critical user flows

### **Security Considerations**

* CSRF protection on all forms
* SQL injection prevention (use Eloquent/Query Builder)
* XSS protection (escape output, sanitize markdown)
* Authorization checks on all routes
* Rate limiting on API endpoints
* Validate and sanitize user input

### **Performance Optimization**

* Eager loading relationships to prevent N+1 queries
* Cache popular discussions and channels
* Queue notification emails
* Database indexes on foreign keys and search fields
* Lazy loading for long discussion threads
* CDN for static assets and avatars

### **Deployment Checklist**

* Set `APP_ENV=production`
* Enable caching: `php artisan config:cache`, `php artisan route:cache`
* Setup queue workers for background jobs
* Configure email service (Mailgun, SendGrid, SES)
* Setup SSL certificate
* Configure backup strategy
* Enable error logging and monitoring (Sentry, Flare)

### **Modern Laravel 12 Specific Updates**

* Use anonymous migrations (no class name required)
* Leverage Eloquent attribute casting v2
* Use model factories with states
* Implement route model binding with custom keys
* Use single-action controllers where appropriate
* Leverage Laravel Pint for code styling
* Use Pest PHP for testing (optional)

