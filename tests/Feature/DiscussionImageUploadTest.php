<?php

use App\Models\Channel;
use App\Models\Discussion;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('user can create discussion with images', function () {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Discussion with images',
        'content' => 'This discussion has images attached',
        'images' => [
            UploadedFile::fake()->image('photo1.jpg', 800, 600),
            UploadedFile::fake()->image('photo2.png', 1024, 768),
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $discussion = Discussion::where('title', 'Discussion with images')->first();

    expect($discussion)->not->toBeNull()
        ->and($discussion->images)->toHaveCount(2);

    // Verify images were stored
    $discussion->images->each(function ($image) {
        expect(Storage::disk('public')->exists($image->path))->toBeTrue();
    });
});

test('user can create discussion without images', function () {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Discussion without images',
        'content' => 'This discussion has no images',
    ]);

    $response->assertRedirect();

    $discussion = Discussion::where('title', 'Discussion without images')->first();

    expect($discussion)->not->toBeNull()
        ->and($discussion->images)->toHaveCount(0);
});

test('discussion image upload validates file type', function () {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Discussion with invalid file',
        'content' => 'Testing validation',
        'images' => [
            UploadedFile::fake()->create('document.pdf', 100),
        ],
    ]);

    $response->assertSessionHasErrors('images.0');
});

test('discussion image upload validates file size', function () {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Discussion with large file',
        'content' => 'Testing validation',
        'images' => [
            UploadedFile::fake()->image('large.jpg')->size(6000), // 6MB
        ],
    ]);

    $response->assertSessionHasErrors('images.0');
});

test('user can edit discussion and add images', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->for($user)->create();

    $response = $this->actingAs($user)->patch(route('discussions.update', $discussion), [
        'channel_id' => $discussion->channel_id,
        'title' => 'Updated title',
        'content' => 'Updated content',
        'images' => [
            UploadedFile::fake()->image('new-photo.jpg'),
        ],
    ]);

    $response->assertRedirect();

    $discussion->refresh();

    expect($discussion->images)->toHaveCount(1);
});

test('user can edit discussion and remove images', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->for($user)->create();

    // Create an image for the discussion
    $image = $discussion->images()->create([
        'filename' => 'test.jpg',
        'path' => 'discussions/test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
        'width' => 800,
        'height' => 600,
    ]);

    Storage::disk('public')->put($image->path, 'fake image content');

    $response = $this->actingAs($user)->patch(route('discussions.update', $discussion), [
        'channel_id' => $discussion->channel_id,
        'title' => $discussion->title,
        'content' => $discussion->content,
        'removed_images' => [$image->id],
    ]);

    $response->assertRedirect();

    expect(Image::find($image->id))->toBeNull()
        ->and(Storage::disk('public')->exists($image->path))->toBeFalse();
});

test('user cannot remove images from other users discussions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $discussion = Discussion::factory()->for($otherUser)->create();

    $image = $discussion->images()->create([
        'filename' => 'test.jpg',
        'path' => 'discussions/test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    $response = $this->actingAs($user)->patch(route('discussions.update', $discussion), [
        'channel_id' => $discussion->channel_id,
        'title' => $discussion->title,
        'content' => $discussion->content,
        'removed_images' => [$image->id],
    ]);

    $response->assertForbidden();

    expect(Image::find($image->id))->not->toBeNull();
});

test('images are deleted when discussion is deleted', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->for($user)->create();

    $image = $discussion->images()->create([
        'filename' => 'test.jpg',
        'path' => 'discussions/test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    Storage::disk('public')->put($image->path, 'fake image content');

    $imageId = $image->id;
    $imagePath = $image->path;

    $this->actingAs($user)->delete(route('discussions.destroy', $discussion));

    expect(Image::find($imageId))->toBeNull()
        ->and(Storage::disk('public')->exists($imagePath))->toBeFalse();
});

test('discussion images are loaded in show view', function () {
    $discussion = Discussion::factory()->create();

    $discussion->images()->create([
        'filename' => 'test.jpg',
        'path' => 'discussions/test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    Storage::disk('public')->put('discussions/test.jpg', 'fake content');

    $response = $this->get(route('discussions.show', $discussion));

    $response->assertSuccessful()
        ->assertSee('discussions/test.jpg');
});

test('images are optimized when uploaded', function () {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    // Create a large image (simulated)
    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Discussion with large image',
        'content' => 'Testing optimization',
        'images' => [
            UploadedFile::fake()->image('large.jpg', 2000, 1500),
        ],
    ]);

    $discussion = Discussion::latest()->first();
    $image = $discussion->images->first();

    // The image should have width/height recorded
    expect($image->width)->not->toBeNull()
        ->and($image->height)->not->toBeNull();
});
