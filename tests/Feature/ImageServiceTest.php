<?php

use App\Models\Discussion;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->imageService = new ImageService;
});

test('can upload image and store metadata', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg', 800, 600);

    $image = $this->imageService->upload($file, $discussion);

    expect($image)->toBeInstanceOf(Image::class)
        ->and($image->filename)->toContain('.jpg')
        ->and($image->mime_type)->toBe('image/jpeg')
        ->and($image->disk)->toBe('public')
        ->and($image->imageable_type)->toBe(Discussion::class)
        ->and($image->imageable_id)->toBe($discussion->id);

    // Verify file was stored
    expect(Storage::disk('public')->exists($image->path))->toBeTrue();
});

test('can set custom disk for upload', function () {
    Storage::fake('custom-disk');

    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg');

    $image = $this->imageService->setDisk('custom-disk')->upload($file, $discussion);

    expect($image->disk)->toBe('custom-disk');
    expect(Storage::disk('custom-disk')->exists($image->path))->toBeTrue();
});

test('validates image type', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $this->imageService->upload($file, $discussion);
})->throws(\InvalidArgumentException::class);

test('validates image size', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('large.jpg')->size(6000); // 6MB

    $this->imageService->upload($file, $discussion);
})->throws(\InvalidArgumentException::class);

test('can upload and optimize image', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('large.jpg', 2000, 1500);

    $image = $this->imageService->uploadAndOptimize($file, $discussion, null, 1200, 85);

    expect($image)->toBeInstanceOf(Image::class)
        ->and($image->width)->not->toBeNull()
        ->and($image->height)->not->toBeNull();
});

test('can set alt text for image', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg');

    $image = $this->imageService->upload($file, $discussion, 'Test alt text');

    expect($image->alt_text)->toBe('Test alt text');
});

test('generates unique filename', function () {
    $discussion = Discussion::factory()->create();
    $file1 = UploadedFile::fake()->image('test.jpg');
    $file2 = UploadedFile::fake()->image('test.jpg');

    $image1 = $this->imageService->upload($file1, $discussion);
    $image2 = $this->imageService->upload($file2, $discussion);

    expect($image1->filename)->not->toBe($image2->filename);
});

test('stores images in model-specific directory', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg');

    $image = $this->imageService->upload($file, $discussion);

    expect($image->path)->toContain('discussions/');
});

test('records image dimensions', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg', 1024, 768);

    $image = $this->imageService->upload($file, $discussion);

    expect($image->width)->toBeGreaterThan(0)
        ->and($image->height)->toBeGreaterThan(0);
});

test('records image file size', function () {
    $discussion = Discussion::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg')->size(500); // 500KB

    $image = $this->imageService->upload($file, $discussion);

    expect($image->size)->toBeGreaterThan(0);
});
