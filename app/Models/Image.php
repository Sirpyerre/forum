<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'order' => 'integer',
        ];
    }

    /**
     * Get the parent imageable model (Discussion, Reply, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL to the image.
     */
    public function url(): string
    {
        // If path is already a full URL (for seeded data), return it directly
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        // For Cloudinary, use the cloudinary URL helper
        if ($this->disk === 'cloudinary') {
            return cloudinary()->getUrl($this->path);
        }

        // For other disks (local, s3, etc.)
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the temporary URL (for S3 private files).
     */
    public function temporaryUrl(int $minutes = 60): string
    {
        // If path is already a full URL, return it directly
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        if ($this->disk === 's3') {
            return Storage::disk($this->disk)->temporaryUrl(
                $this->path,
                now()->addMinutes($minutes)
            );
        }

        // Cloudinary URLs are always accessible (no temporary URL needed)
        if ($this->disk === 'cloudinary') {
            return $this->url();
        }

        return $this->url();
    }

    /**
     * Delete the image file from storage.
     */
    public function deleteFile(): bool
    {
        // Don't try to delete external URLs (seeded data)
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return true;
        }

        return Storage::disk($this->disk)->delete($this->path);
    }

    /**
     * Check if the image is an allowed type.
     */
    public function isAllowedType(): bool
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        return in_array($this->mime_type, $allowedTypes);
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Image $image) {
            $image->deleteFile();
        });
    }
}
