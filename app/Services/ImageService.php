<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as ImageIntervention;

class ImageService
{
    /**
     * Maximum file size in KB (5MB).
     */
    private const MAX_FILE_SIZE = 5120;

    /**
     * Allowed mime types.
     */
    private const ALLOWED_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Storage disk to use (can be changed via config).
     */
    private string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.images', 'public');
    }

    /**
     * Upload and attach image to a model.
     */
    public function upload(UploadedFile $file, Model $model, ?string $altText = null): Image
    {
        $this->validate($file);

        $filename = $this->generateFilename($file);
        $path = $this->getStoragePath($model);

        // Get image dimensions
        $imageData = getimagesize($file->getRealPath());
        $width = $imageData[0] ?? null;
        $height = $imageData[1] ?? null;

        // Store the file
        $storedPath = Storage::disk($this->disk)->putFileAs(
            $path,
            $file,
            $filename
        );

        // Create image record
        return $model->images()->create([
            'filename' => $filename,
            'path' => $storedPath,
            'disk' => $this->disk,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'alt_text' => $altText,
            'order' => $model->images()->count(),
        ]);
    }

    /**
     * Upload and optimize image (resize if needed).
     */
    public function uploadAndOptimize(
        UploadedFile $file,
        Model $model,
        ?string $altText = null,
        int $maxWidth = 1200,
        int $quality = 85
    ): Image {
        $this->validate($file);

        // Only optimize if image is larger than max width
        $imageData = getimagesize($file->getRealPath());
        $width = $imageData[0];
        $height = $imageData[1];

        if ($width > $maxWidth) {
            $file = $this->resizeImage($file, $maxWidth, $quality);
        }

        return $this->upload($file, $model, $altText);
    }

    /**
     * Upload multiple images at once.
     *
     * @param  array<UploadedFile>  $files
     * @return \Illuminate\Support\Collection<Image>
     */
    public function uploadMultiple(array $files, Model $model): \Illuminate\Support\Collection
    {
        $images = collect();

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $images->push($this->upload($file, $model));
            }
        }

        return $images;
    }

    /**
     * Delete an image.
     */
    public function delete(Image $image): bool
    {
        return $image->delete();
    }

    /**
     * Validate uploaded file.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validate(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_TYPES)) {
            throw new \InvalidArgumentException('Invalid image type. Allowed: jpg, png, gif, webp');
        }

        if ($file->getSize() > (self::MAX_FILE_SIZE * 1024)) {
            throw new \InvalidArgumentException('Image size exceeds '.self::MAX_FILE_SIZE.'KB');
        }
    }

    /**
     * Generate unique filename.
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $basename = Str::slug($basename);

        return $basename.'-'.Str::random(8).'.'.$extension;
    }

    /**
     * Get storage path based on model type.
     */
    private function getStoragePath(Model $model): string
    {
        $modelName = class_basename($model);
        $year = now()->format('Y');
        $month = now()->format('m');

        return strtolower($modelName)."s/{$year}/{$month}";
    }

    /**
     * Resize image (requires intervention/image package).
     */
    private function resizeImage(UploadedFile $file, int $maxWidth, int $quality): UploadedFile
    {
        // This method requires intervention/image package
        // If not installed, return original file
        if (! class_exists(ImageIntervention::class)) {
            return $file;
        }

        $img = ImageIntervention::read($file->getRealPath());
        $img->scale(width: $maxWidth);

        // Save to temp file
        $tempPath = sys_get_temp_dir().'/'.uniqid('img_').'.'.$file->getClientOriginalExtension();
        $img->save($tempPath, quality: $quality);

        return new UploadedFile(
            $tempPath,
            $file->getClientOriginalName(),
            $file->getMimeType(),
            null,
            true
        );
    }

    /**
     * Change storage disk (useful for testing or switching to S3/Cloudinary).
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Get current disk.
     */
    public function getDisk(): string
    {
        return $this->disk;
    }
}
