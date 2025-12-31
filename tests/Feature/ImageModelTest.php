<?php

use App\Models\Discussion;
use App\Models\Image;
use App\Models\Reply;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('image belongs to imageable model', function () {
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
    ]);

    expect($image->imageable)->toBeInstanceOf(Discussion::class)
        ->and($image->imageable->id)->toBe($discussion->id);
});

test('image can generate url', function () {
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'disk' => 'public',
        'path' => 'test/image.jpg',
    ]);

    Storage::disk('public')->put('test/image.jpg', 'content');

    expect($image->url())->toContain('test/image.jpg');
});

test('image can generate temporary url for s3', function () {
    Storage::fake('s3');
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'disk' => 's3',
        'path' => 'test/image.jpg',
    ]);

    Storage::disk('s3')->put('test/image.jpg', 'content');

    $tempUrl = $image->temporaryUrl(60);

    expect($tempUrl)->toBeString();
});

test('image temporary url falls back to regular url for non-s3 disks', function () {
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'disk' => 'public',
        'path' => 'test/image.jpg',
    ]);

    Storage::disk('public')->put('test/image.jpg', 'content');

    expect($image->temporaryUrl())->toBe($image->url());
});

test('image can delete its file', function () {
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'disk' => 'public',
        'path' => 'test/image.jpg',
    ]);

    Storage::disk('public')->put('test/image.jpg', 'content');

    expect(Storage::disk('public')->exists('test/image.jpg'))->toBeTrue();

    $result = $image->deleteFile();

    expect($result)->toBeTrue()
        ->and(Storage::disk('public')->exists('test/image.jpg'))->toBeFalse();
});

test('image file is deleted when model is deleted', function () {
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'disk' => 'public',
        'path' => 'test/image.jpg',
    ]);

    Storage::disk('public')->put('test/image.jpg', 'content');

    expect(Storage::disk('public')->exists('test/image.jpg'))->toBeTrue();

    $image->delete();

    expect(Storage::disk('public')->exists('test/image.jpg'))->toBeFalse();
});

test('image can check if type is allowed', function () {
    $discussion = Discussion::factory()->create();

    $allowedImage = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'mime_type' => 'image/jpeg',
    ]);

    $disallowedImage = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'mime_type' => 'application/pdf',
    ]);

    expect($allowedImage->isAllowedType())->toBeTrue()
        ->and($disallowedImage->isAllowedType())->toBeFalse();
});

test('image can get formatted size', function () {
    $discussion = Discussion::factory()->create();

    $image1 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'size' => 1024,
    ]); // 1KB
    $image2 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'size' => 1024 * 1024,
    ]); // 1MB
    $image3 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'size' => 500,
    ]); // 500B

    expect($image1->getFormattedSize())->toContain('KB')
        ->and($image2->getFormattedSize())->toContain('MB')
        ->and($image3->getFormattedSize())->toContain('B');
});

test('image has correct fillable fields', function () {
    $image = new Image;

    $fillable = $image->getFillable();

    expect($fillable)->toContain('filename')
        ->and($fillable)->toContain('path')
        ->and($fillable)->toContain('disk')
        ->and($fillable)->toContain('mime_type')
        ->and($fillable)->toContain('size')
        ->and($fillable)->toContain('width')
        ->and($fillable)->toContain('height')
        ->and($fillable)->toContain('alt_text')
        ->and($fillable)->toContain('order');
});

test('image casts attributes correctly', function () {
    $discussion = Discussion::factory()->create();

    $image = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'size' => '1024',
        'width' => '800',
        'height' => '600',
        'order' => '1',
    ]);

    expect($image->size)->toBeInt()
        ->and($image->width)->toBeInt()
        ->and($image->height)->toBeInt()
        ->and($image->order)->toBeInt();
});

test('discussion can have multiple images', function () {
    $discussion = Discussion::factory()->create();

    $image1 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'order' => 0,
    ]);

    $image2 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'order' => 1,
    ]);

    expect($discussion->images)->toHaveCount(2)
        ->and($discussion->images->first()->id)->toBe($image1->id)
        ->and($discussion->images->last()->id)->toBe($image2->id);
});

test('reply can have multiple images', function () {
    $reply = Reply::factory()->create();

    $image1 = Image::factory()->create([
        'imageable_type' => Reply::class,
        'imageable_id' => $reply->id,
    ]);

    $image2 = Image::factory()->create([
        'imageable_type' => Reply::class,
        'imageable_id' => $reply->id,
    ]);

    expect($reply->images)->toHaveCount(2);
});

test('images are ordered by order column', function () {
    $discussion = Discussion::factory()->create();

    $image2 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'order' => 2,
        'filename' => 'second.jpg',
    ]);

    $image1 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'order' => 1,
        'filename' => 'first.jpg',
    ]);

    $image3 = Image::factory()->create([
        'imageable_type' => Discussion::class,
        'imageable_id' => $discussion->id,
        'order' => 3,
        'filename' => 'third.jpg',
    ]);

    $orderedImages = $discussion->fresh()->images;

    expect($orderedImages->first()->filename)->toBe('first.jpg')
        ->and($orderedImages->get(1)->filename)->toBe('second.jpg')
        ->and($orderedImages->last()->filename)->toBe('third.jpg');
});
