<?php

use App\Models\Discussion;
use App\Models\Image;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('user can create reply with images', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'This is a reply with images',
        'images' => [
            UploadedFile::fake()->image('reply1.jpg'),
            UploadedFile::fake()->image('reply2.png'),
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $reply = Reply::latest()->first();

    expect($reply)->not->toBeNull()
        ->and($reply->images)->toHaveCount(2);

    // Verify images were stored
    $reply->images->each(function ($image) {
        expect(Storage::disk('public')->exists($image->path))->toBeTrue();
    });
});

test('user can create reply without images', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'This is a reply without images',
    ]);

    $response->assertRedirect();

    $reply = Reply::latest()->first();

    expect($reply)->not->toBeNull()
        ->and($reply->images)->toHaveCount(0);
});

test('reply image upload validates file type', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'Testing validation',
        'images' => [
            UploadedFile::fake()->create('document.txt', 100),
        ],
    ]);

    $response->assertSessionHasErrors('images.0');
});

test('reply image upload validates file size', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'Testing validation',
        'images' => [
            UploadedFile::fake()->image('large.jpg')->size(6000), // 6MB
        ],
    ]);

    $response->assertSessionHasErrors('images.0');
});

test('user can update reply and add images', function () {
    $user = User::factory()->create();
    $reply = Reply::factory()->for($user)->create();

    $response = $this->actingAs($user)->patch(route('replies.update', $reply), [
        'content' => 'Updated reply content',
        'images' => [
            UploadedFile::fake()->image('new-reply-photo.jpg'),
        ],
    ]);

    $response->assertRedirect();

    $reply->refresh();

    expect($reply->images)->toHaveCount(1);
});

test('user can update reply and remove images', function () {
    $user = User::factory()->create();
    $reply = Reply::factory()->for($user)->create();

    $image = $reply->images()->create([
        'filename' => 'test.jpg',
        'path' => 'replies/test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    Storage::disk('public')->put($image->path, 'fake image content');

    $response = $this->actingAs($user)->patch(route('replies.update', $reply), [
        'content' => $reply->content,
        'removed_images' => [$image->id],
    ]);

    $response->assertRedirect();

    expect(Image::find($image->id))->toBeNull()
        ->and(Storage::disk('public')->exists($image->path))->toBeFalse();
});

test('user cannot update other users replies', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $reply = Reply::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)->patch(route('replies.update', $reply), [
        'content' => 'Trying to update',
    ]);

    $response->assertForbidden();
});

test('images are deleted when reply is deleted', function () {
    $user = User::factory()->create();
    $reply = Reply::factory()->for($user)->create();

    $image = $reply->images()->create([
        'filename' => 'test.jpg',
        'path' => 'replies/test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    Storage::disk('public')->put($image->path, 'fake image content');

    $imageId = $image->id;
    $imagePath = $image->path;

    $this->actingAs($user)->delete(route('replies.destroy', $reply));

    expect(Image::find($imageId))->toBeNull()
        ->and(Storage::disk('public')->exists($imagePath))->toBeFalse();
});

test('reply images are displayed in discussion view', function () {
    $discussion = Discussion::factory()->create();
    $reply = Reply::factory()->for($discussion)->create();

    $reply->images()->create([
        'filename' => 'reply-test.jpg',
        'path' => 'replies/reply-test.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    Storage::disk('public')->put('replies/reply-test.jpg', 'fake content');

    $response = $this->get(route('discussions.show', $discussion));

    $response->assertSuccessful()
        ->assertSee('replies/reply-test.jpg');
});

test('reply awards points when created', function () {
    $user = User::factory()->create(['points' => 100]);
    $discussion = Discussion::factory()->create();

    $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'This is a reply',
    ]);

    $user->refresh();

    expect($user->points)->toBe(103); // 100 + 3 for reply
});

test('guest cannot create reply with images', function () {
    $discussion = Discussion::factory()->create();

    $response = $this->post(route('replies.store', $discussion), [
        'content' => 'Guest reply',
        'images' => [
            UploadedFile::fake()->image('guest.jpg'),
        ],
    ]);

    $response->assertRedirect(route('login'));
});
