# Image Upload System

Complete documentation for the Laravel Forum Image Upload System.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Storage Drivers](#storage-drivers)
- [Components](#components)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## Overview

The image upload system provides a scalable, multi-driver solution for handling image uploads in Discussions and Replies. Built with Laravel's filesystem abstraction, it supports local storage, AWS S3, and Cloudinary out of the box.

### Key Features

- 🖼️ **Multi-image uploads** with drag & drop
- 🎨 **Automatic optimization** (resize, compression)
- 💾 **Multi-driver storage** (local, S3, Cloudinary)
- 🗑️ **Automatic cleanup** when models are deleted
- 🔒 **Validation** (file type, size limits)
- 📱 **Responsive gallery** with lightbox
- ⚡ **Real-time preview** using Livewire
- 🔐 **Authorization** checks

## Installation

### Requirements

- PHP 8.2+
- Laravel 12+
- Intervention Image Laravel 1.5+
- Livewire 3+

### Setup

1. **Run migrations:**
   ```bash
   php artisan migrate
   ```

2. **Create storage symlink:**
   ```bash
   php artisan storage:link
   ```

3. **Install Intervention Image (already installed):**
   ```bash
   composer require intervention/image-laravel
   ```

## Configuration

### Environment Variables

Add to your `.env` file:

```env
# Image Storage Driver (public, s3, cloudinary)
IMAGES_DISK=public

# AWS S3 (if using S3)
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name

# Cloudinary (if using Cloudinary)
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

### Storage Configuration

Image storage is configured in `config/filesystems.php`:

```php
'images' => env('IMAGES_DISK', 'public'),
```

Available disks:
- `public` - Local storage (default)
- `s3` - Amazon S3
- `cloudinary` - Cloudinary CDN

### Image Limits

**Discussions:**
- Max files: 5
- Max size: 5MB per image
- Allowed types: JPG, PNG, GIF, WebP

**Replies:**
- Max files: 3
- Max size: 5MB per image
- Allowed types: JPG, PNG, GIF, WebP

**Optimization:**
- Max width: 1200px (auto-resize)
- Quality: 85%

## Usage

### For End Users

#### Upload Images to Discussion

1. Navigate to **Create Discussion** or **Edit Discussion**
2. Fill in title, channel, and content
3. Scroll to the **Attach Images** section
4. Either:
   - Click to browse files
   - Drag and drop images directly
5. Images will preview immediately
6. Click ❌ to remove unwanted images
7. Submit the form

#### Upload Images to Reply

1. Navigate to a discussion
2. Scroll to **Post a Reply** section
3. Write your reply content
4. Click **Attach Images** below the editor
5. Upload images (same as above)
6. Submit the reply

#### View Images

- Images appear as a gallery below the content
- Click any image to open lightbox
- Use arrow keys (←/→) or buttons to navigate
- Press ESC to close lightbox

### For Developers

#### Using the ImageService

```php
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;

// Basic upload
$imageService = new ImageService();
$image = $imageService->upload($file, $discussion);

// Upload with optimization
$image = $imageService->uploadAndOptimize(
    file: $file,
    model: $discussion,
    altText: 'Screenshot of bug',
    maxWidth: 1200,
    quality: 85
);

// Set custom storage disk
$image = $imageService
    ->setDisk('s3')
    ->upload($file, $discussion);
```

#### Accessing Images in Models

```php
// Discussion model
$discussion->images; // Collection of images
$discussion->images()->count(); // Count images

// Reply model
$reply->images; // Collection of images

// Image model
$image->url(); // Get public URL
$image->temporaryUrl(60); // Get temporary URL (S3)
$image->deleteFile(); // Delete from storage
$image->getFormattedSize(); // Human-readable size
```

#### Adding Images to Custom Models

1. **Add relationship to your model:**

```php
use Illuminate\Database\Eloquent\Relations\MorphMany;

public function images(): MorphMany
{
    return $this->morphMany(Image::class, 'imageable')->orderBy('order');
}

// Add cascade deletion
protected static function booted(): void
{
    static::deleting(function ($model) {
        $model->images()->get()->each->delete();
    });
}
```

2. **Update controller:**

```php
use App\Services\ImageService;

public function store(Request $request, ImageService $imageService)
{
    $validated = $request->validate([
        'images.*' => 'nullable|image|max:5120',
    ]);

    $model = YourModel::create([...]);

    if ($request->hasFile('images')) {
        $disk = config('filesystems.images');
        foreach ($request->file('images') as $image) {
            $imageService->setDisk($disk)->uploadAndOptimize(
                $image,
                $model,
                null,
                1200,
                85
            );
        }
    }

    return redirect()->back();
}
```

3. **Add to view:**

```blade
@livewire('components.image-uploader', [
    'maxFiles' => 5,
    'label' => 'Upload Images',
    'help' => 'Max 5MB per image.'
])

<!-- Display images -->
@if($model->images->count() > 0)
    @livewire('components.image-gallery', [
        'images' => $model->images,
        'columns' => 4
    ])
@endif
```

## Storage Drivers

### Local Storage (Default)

Files stored in `storage/app/public/`.

**Pros:**
- Zero configuration
- No external dependencies
- Free

**Cons:**
- Limited scalability
- No CDN
- Files lost on server restart (cloud platforms)

### Amazon S3

Scalable cloud storage with CDN integration.

**Setup:**

1. Install AWS SDK:
   ```bash
   composer require league/flysystem-aws-s3-v3
   ```

2. Configure `.env`:
   ```env
   IMAGES_DISK=s3
   AWS_ACCESS_KEY_ID=your_key
   AWS_SECRET_ACCESS_KEY=your_secret
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=your_bucket_name
   ```

3. Set bucket permissions (public read)

**Pros:**
- Highly scalable
- CDN integration (CloudFront)
- Automatic backups
- Pay per use

**Cons:**
- Requires AWS account
- Configuration complexity
- Costs (minimal for small projects)

### Cloudinary

Image CDN with transformation capabilities.

**Setup:**

Cloudinary is already installed and configured! Just add your credentials:

1. Get your credentials from [Cloudinary Dashboard](https://cloudinary.com/console)

2. Configure `.env`:
   ```env
   IMAGES_DISK=cloudinary
   CLOUDINARY_CLOUD_NAME=your_cloud_name
   CLOUDINARY_API_KEY=your_api_key
   CLOUDINARY_API_SECRET=your_api_secret
   ```

3. Images will be stored in organized folders:
   ```
   convene/{user_id}/{discussion_id}/{timestamp}/image.jpg
   ```

   Example: `convene/1/5/20251231_143022/screenshot-a1b2c3d4.jpg`

**Pros:**
- Built-in image transformations
- Automatic optimization
- CDN included
- Free tier (25GB/month)
- Organized folder structure
- Already integrated with custom driver

**Cons:**
- Requires Cloudinary account
- Vendor lock-in
- Costs beyond free tier

**Technical Details:**

The application uses a custom `CloudinaryServiceProvider` that extends Laravel's filesystem to support Cloudinary as a first-class driver. Images are organized by user and discussion for easy management in the Cloudinary dashboard.

### Switching Drivers

Change storage driver without code changes:

```bash
# .env
IMAGES_DISK=s3  # or 'cloudinary' or 'public'
```

Existing images won't migrate automatically. You'll need to:
1. Export images from old driver
2. Import to new driver
3. Update database paths

## Components

### Image Uploader Component

Livewire component for uploading images.

**Location:** `resources/views/livewire/components/image-uploader.blade.php`

**Props:**
- `existingImages` - Collection of existing images (for edit forms)
- `maxFiles` - Maximum number of files (default: 5)
- `maxSize` - Maximum size in KB (default: 5120)
- `label` - Field label (default: 'Upload Images')
- `help` - Help text (default: 'Supported: JPG, PNG...')

**Usage:**
```blade
@livewire('components.image-uploader', [
    'existingImages' => $discussion->images,
    'maxFiles' => 5,
    'label' => 'Attach Images',
    'help' => 'Max 5MB per image, 5 images total.'
])
```

**Features:**
- Drag & drop upload
- Real-time preview
- Remove before upload
- File validation
- Progress indicator

### Image Gallery Component

Livewire component for displaying images with lightbox.

**Location:** `resources/views/livewire/components/image-gallery.blade.php`

**Props:**
- `images` - Collection of Image models
- `columns` - Grid columns: 2, 3, 4, or 5 (default: 4)

**Usage:**
```blade
@livewire('components.image-gallery', [
    'images' => $discussion->images,
    'columns' => 4
])
```

**Features:**
- Responsive grid layout
- Lightbox modal
- Keyboard navigation (←/→/ESC)
- Image metadata display
- Dark mode support

## API Reference

### ImageService

**Methods:**

#### `upload(UploadedFile $file, Model $model, ?string $altText = null): Image`

Upload an image without optimization.

```php
$image = $imageService->upload($file, $discussion, 'Alt text');
```

#### `uploadAndOptimize(UploadedFile $file, Model $model, ?string $altText, int $maxWidth, int $quality): Image`

Upload and optimize an image.

```php
$image = $imageService->uploadAndOptimize($file, $discussion, null, 1200, 85);
```

#### `setDisk(string $disk): self`

Set the storage disk for upload.

```php
$imageService->setDisk('s3')->upload($file, $discussion);
```

### Image Model

**Methods:**

#### `url(): string`

Get the public URL of the image.

```php
$url = $image->url();
// https://example.com/storage/discussions/image.jpg
```

#### `temporaryUrl(int $minutes = 60): string`

Get a temporary signed URL (S3 only).

```php
$url = $image->temporaryUrl(30);
```

#### `deleteFile(): bool`

Delete the image file from storage.

```php
$image->deleteFile();
```

#### `getFormattedSize(): string`

Get human-readable file size.

```php
$size = $image->getFormattedSize();
// "1.5 MB"
```

#### `isAllowedType(): bool`

Check if the image type is allowed.

```php
if ($image->isAllowedType()) {
    // Process image
}
```

**Relationships:**

```php
$image->imageable; // Discussion or Reply
```

**Attributes:**

- `filename` - Original filename
- `path` - Storage path
- `disk` - Storage disk name
- `mime_type` - MIME type (image/jpeg)
- `size` - File size in bytes
- `width` - Image width in pixels
- `height` - Image height in pixels
- `alt_text` - Alternative text
- `order` - Display order

## Testing

Run image upload tests:

```bash
# All image tests
php artisan test --filter=Image

# Specific test suites
php artisan test tests/Feature/DiscussionImageUploadTest.php
php artisan test tests/Feature/ReplyImageUploadTest.php
php artisan test tests/Feature/ImageServiceTest.php
php artisan test tests/Feature/ImageModelTest.php
```

### Test Coverage

- ✅ 44 tests, 107 assertions
- ✅ Discussion image uploads
- ✅ Reply image uploads
- ✅ File validation
- ✅ Image optimization
- ✅ Cascade deletion
- ✅ Authorization
- ✅ Storage operations
- ✅ Multi-driver support

## Troubleshooting

### Images Not Displaying

**Check storage link:**
```bash
php artisan storage:link
```

**Verify permissions:**
```bash
chmod -R 775 storage/app/public
```

**Check disk configuration:**
```php
// config/filesystems.php
'images' => env('IMAGES_DISK', 'public'),
```

### Upload Fails Silently

**Check PHP upload limits:**
```ini
; php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

**Check Laravel validation:**
```php
// Increase max size in controller
'images.*' => 'nullable|image|max:10240', // 10MB
```

### Images Not Deleting

**Verify model observer:**
```php
// In Discussion or Reply model
protected static function booted(): void
{
    static::deleting(function ($model) {
        $model->images()->get()->each->delete();
    });
}
```

### S3 Upload Fails

**Check credentials:**
```bash
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'test');
```

**Verify bucket permissions:**
- Enable public read access
- Check CORS configuration
- Verify IAM user permissions

### Cloudinary Upload Fails

**Test connection:**
```bash
php artisan tinker
>>> config('cloudinary.cloud_name')
```

**Check API credentials:**
- Verify cloud name
- Check API key/secret
- Test in Cloudinary dashboard

### Performance Issues

**Optimize large images:**
```php
// Reduce quality
$imageService->uploadAndOptimize($file, $model, null, 800, 70);
```

**Use CDN:**
- Switch to S3 + CloudFront
- Use Cloudinary
- Enable browser caching

**Queue uploads:**
```php
// Create a job for processing
ProcessImageUpload::dispatch($file, $model);
```

## Best Practices

1. **Always validate uploads** - Never trust user input
2. **Use appropriate storage** - S3/Cloudinary for production
3. **Optimize images** - Reduce size before storing
4. **Set alt text** - Improve accessibility and SEO
5. **Clean up orphans** - Implement cascade deletion
6. **Monitor storage** - Track usage and costs
7. **Backup important images** - Regular backups
8. **Test thoroughly** - Run test suite before deploy

## Migration Guide

### From Local to S3

1. Export existing images:
   ```bash
   php artisan storage:export s3
   ```

2. Update `.env`:
   ```env
   IMAGES_DISK=s3
   ```

3. Test uploads:
   ```bash
   php artisan test --filter=ImageService
   ```

### From S3 to Cloudinary

1. Install Cloudinary SDK
2. Create migration script:
   ```php
   Image::chunk(100, function ($images) {
       foreach ($images as $image) {
           // Download from S3
           $file = Storage::disk('s3')->get($image->path);

           // Upload to Cloudinary
           $result = Cloudinary::upload($file);

           // Update database
           $image->update([
               'disk' => 'cloudinary',
               'path' => $result->getPublicId(),
           ]);
       }
   });
   ```

3. Update `.env`:
   ```env
   IMAGES_DISK=cloudinary
   ```

## Support

For issues or questions:
- Check [GitHub Issues](https://github.com/your-repo/issues)
- Review [Laravel Filesystem Docs](https://laravel.com/docs/filesystem)
- Check [Intervention Image Docs](https://image.intervention.io/)

---

**Version:** 1.0.0
**Last Updated:** December 30, 2025
**Maintained By:** Laravel Forum Team
