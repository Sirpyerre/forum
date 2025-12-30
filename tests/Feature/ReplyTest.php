<?php

declare(strict_types=1);

use App\Models\Discussion;
use App\Models\Reply;
use App\Models\User;

test('authenticated user can post a reply', function () {
    $user = User::factory()->create();
    $discussion = Discussion::factory()->create();

    $response = $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'This is a test reply with enough content.',
    ]);

    $response->assertRedirect(route('discussions.show', $discussion));
    $response->assertSessionHas('success');

    expect(Reply::count())->toBe(1);
    expect($discussion->replies()->first()->content)->toBe('This is a test reply with enough content.');
});

test('reply awards points to user', function () {
    $user = User::factory()->create(['points' => 50]);
    $discussion = Discussion::factory()->create();
    $initialPoints = $user->points;

    $this->actingAs($user)->post(route('replies.store', $discussion), [
        'content' => 'This is a test reply with enough content.',
    ]);

    $user->refresh();
    expect($user->points)->toBe($initialPoints + 3);
});

test('discussion owner can mark reply as best answer', function () {
    $discussionOwner = User::factory()->create();
    $replyAuthor = User::factory()->create();
    $discussion = Discussion::factory()->create(['user_id' => $discussionOwner->id]);
    $reply = Reply::factory()->create([
        'discussion_id' => $discussion->id,
        'user_id' => $replyAuthor->id,
    ]);

    $response = $this->actingAs($discussionOwner)
        ->post(route('replies.best-answer', $reply));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $reply->refresh();
    expect($reply->best_answer)->toBeTrue();
});

test('non-owner cannot mark reply as best answer', function () {
    $discussionOwner = User::factory()->create();
    $replyAuthor = User::factory()->create();
    $otherUser = User::factory()->create();
    $discussion = Discussion::factory()->create(['user_id' => $discussionOwner->id]);
    $reply = Reply::factory()->create([
        'discussion_id' => $discussion->id,
        'user_id' => $replyAuthor->id,
    ]);

    $response = $this->actingAs($otherUser)
        ->post(route('replies.best-answer', $reply));

    $response->assertForbidden();

    $reply->refresh();
    expect($reply->best_answer)->toBeFalse();
});

test('best answer awards points to reply author and discussion owner', function () {
    $discussionOwner = User::factory()->create(['points' => 50]);
    $replyAuthor = User::factory()->create(['points' => 50]);
    $discussion = Discussion::factory()->create(['user_id' => $discussionOwner->id]);
    $reply = Reply::factory()->create([
        'discussion_id' => $discussion->id,
        'user_id' => $replyAuthor->id,
    ]);

    $ownerInitialPoints = $discussionOwner->points;
    $authorInitialPoints = $replyAuthor->points;

    $this->actingAs($discussionOwner)
        ->post(route('replies.best-answer', $reply));

    $discussionOwner->refresh();
    $replyAuthor->refresh();

    expect($replyAuthor->points)->toBe($authorInitialPoints + 15);
    expect($discussionOwner->points)->toBe($ownerInitialPoints + 5);
});

test('only one reply can be marked as best answer per discussion', function () {
    $discussionOwner = User::factory()->create();
    $discussion = Discussion::factory()->create(['user_id' => $discussionOwner->id]);
    $reply1 = Reply::factory()->create(['discussion_id' => $discussion->id]);
    $reply2 = Reply::factory()->create(['discussion_id' => $discussion->id]);

    // Mark first reply as best answer
    $this->actingAs($discussionOwner)
        ->post(route('replies.best-answer', $reply1));

    $reply1->refresh();
    expect($reply1->best_answer)->toBeTrue();

    // Mark second reply as best answer
    $this->actingAs($discussionOwner)
        ->post(route('replies.best-answer', $reply2));

    $reply1->refresh();
    $reply2->refresh();

    // First reply should no longer be best answer
    expect($reply1->best_answer)->toBeFalse();
    expect($reply2->best_answer)->toBeTrue();
});

test('user can like a reply', function () {
    $user = User::factory()->create();
    $reply = Reply::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('replies.like', $reply));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($reply->likes()->count())->toBe(1);
    expect($reply->likes()->first()->user_id)->toBe($user->id);
});

test('user can unlike a reply', function () {
    $user = User::factory()->create();
    $reply = Reply::factory()->create();

    // Like the reply first
    $this->actingAs($user)->post(route('replies.like', $reply));
    expect($reply->likes()->count())->toBe(1);

    // Unlike the reply
    $response = $this->actingAs($user)->post(route('replies.like', $reply));

    $response->assertRedirect();
    expect($reply->likes()->count())->toBe(0);
});

test('like awards points to reply author', function () {
    $user = User::factory()->create();
    $replyAuthor = User::factory()->create(['points' => 50]);
    $reply = Reply::factory()->create(['user_id' => $replyAuthor->id]);
    $initialPoints = $replyAuthor->points;

    $this->actingAs($user)->post(route('replies.like', $reply));

    $replyAuthor->refresh();
    expect($replyAuthor->points)->toBe($initialPoints + 2);
});

test('unlike deducts points from reply author', function () {
    $user = User::factory()->create();
    $replyAuthor = User::factory()->create(['points' => 50]);
    $reply = Reply::factory()->create(['user_id' => $replyAuthor->id]);

    // Like first
    $this->actingAs($user)->post(route('replies.like', $reply));
    $replyAuthor->refresh();
    $pointsAfterLike = $replyAuthor->points;

    // Unlike
    $this->actingAs($user)->post(route('replies.like', $reply));

    $replyAuthor->refresh();
    expect($replyAuthor->points)->toBe($pointsAfterLike - 2);
});
