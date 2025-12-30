<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\Discussion;
use App\Models\User;

test('authenticated user can create a discussion with markdown', function () {
    $user = User::factory()->create(['points' => 50]);
    $channel = Channel::factory()->create();
    $initialPoints = $user->points;

    $markdownContent = <<<'MARKDOWN'
    # This is a heading

    This is **bold text** and this is *italic text*.

    Here is some `inline code`.

    ```php
    function hello() {
        return 'world';
    }
    ```

    - Item 1
    - Item 2
    - Item 3
    MARKDOWN;

    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Test Discussion with Markdown',
        'content' => $markdownContent,
    ]);

    $response->assertRedirect();

    $discussion = Discussion::where('title', 'Test Discussion with Markdown')->first();
    expect($discussion)->not->toBeNull();
    expect($discussion->content)->toBe($markdownContent);
    expect($discussion->user_id)->toBe($user->id);
    expect($discussion->channel_id)->toBe($channel->id);

    // Verify user received 5 points for creating discussion
    $user->refresh();
    expect($user->points)->toBe($initialPoints + 5);
});

test('discussion markdown is rendered correctly on show page', function () {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $discussion = Discussion::factory()->create([
        'user_id' => $user->id,
        'channel_id' => $channel->id,
        'title' => 'Markdown Test',
        'content' => 'This is **bold** and this is *italic* and this is `code`.',
    ]);

    $response = $this->get(route('discussions.show', $discussion));

    $response->assertSuccessful();
    $response->assertSee('Markdown Test');
    // The markdown should be rendered as HTML
    $response->assertSee('<strong>bold</strong>', false);
    $response->assertSee('<em>italic</em>', false);
    $response->assertSee('<code>code</code>', false);
});

test('unauthenticated user cannot create a discussion', function () {
    $channel = Channel::factory()->create();

    $response = $this->post(route('discussions.store'), [
        'channel_id' => $channel->id,
        'title' => 'Test Discussion',
        'content' => 'This should not be created',
    ]);

    $response->assertRedirect(route('login'));

    expect(Discussion::count())->toBe(0);
});

test('discussion requires valid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('discussions.store'), [
        'channel_id' => 999, // Non-existent channel
        'title' => '', // Empty title
        'content' => 'short', // Too short (min 10 chars)
    ]);

    $response->assertSessionHasErrors(['channel_id', 'title', 'content']);

    expect(Discussion::count())->toBe(0);
});

test('user can view create discussion form', function () {
    $user = User::factory()->create();
    Channel::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('discussions.create'));

    $response->assertSuccessful();
    $response->assertSee('Create New Discussion');
    $response->assertViewHas('channels');
});

test('unauthenticated user cannot access create discussion form', function () {
    $response = $this->get(route('discussions.create'));

    $response->assertRedirect(route('login'));
});
