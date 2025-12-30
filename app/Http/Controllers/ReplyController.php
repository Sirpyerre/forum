<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Discussion;
use App\Models\Reply;
use App\Notifications\BestAnswerNotification;
use App\Notifications\NewReplyNotification;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * Store a new reply to a discussion.
     */
    public function store(Request $request, Discussion $discussion)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $reply = $discussion->replies()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        // Award points for posting a reply
        auth()->user()->increment('points', 3);

        // Award "First Reply" badge if this is the user's first reply
        if (auth()->user()->replies()->count() === 1) {
            $firstReplyBadge = Badge::where('slug', 'first-reply')->first();
            if ($firstReplyBadge) {
                auth()->user()->badges()->syncWithoutDetaching($firstReplyBadge);
            }
        }

        // Notify watchers about new reply (excluding the reply author)
        $watchers = $discussion->watchers()
            ->with('user')
            ->where('user_id', '!=', auth()->id())
            ->get();

        foreach ($watchers as $watcher) {
            $watcher->user->notify(new NewReplyNotification($reply, $discussion));
        }

        return redirect()
            ->route('discussions.show', $discussion)
            ->with('success', 'Reply posted successfully!');
    }

    /**
     * Update the specified reply.
     */
    public function update(Request $request, Reply $reply)
    {
        $this->authorize('update', $reply);

        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $reply->update($validated);

        return redirect()
            ->route('discussions.show', $reply->discussion)
            ->with('success', 'Reply updated successfully!');
    }

    /**
     * Remove the specified reply.
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('delete', $reply);

        $discussion = $reply->discussion;
        $reply->delete();

        return redirect()
            ->route('discussions.show', $discussion)
            ->with('success', 'Reply deleted successfully!');
    }

    /**
     * Toggle like on a reply.
     */
    public function like(Reply $reply)
    {
        $user = auth()->user();

        // Check if user already liked this reply
        $existingLike = $reply->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            // Unlike: remove the like and deduct points
            $existingLike->delete();
            $reply->user->decrement('points', 2);

            return back()->with('success', 'Reply unliked!');
        } else {
            // Like: create the like and award points
            $reply->likes()->create([
                'user_id' => $user->id,
            ]);
            $reply->user->increment('points', 2);

            return back()->with('success', 'Reply liked!');
        }
    }

    /**
     * Mark a reply as the best answer.
     */
    public function markBestAnswer(Reply $reply)
    {
        $discussion = $reply->discussion;

        // Only discussion owner can mark best answer
        $this->authorize('update', $discussion);

        // Remove best answer from any other reply in this discussion
        $discussion->replies()->update(['best_answer' => false]);

        // Mark this reply as best answer
        $reply->update(['best_answer' => true]);

        // Award points for best answer
        $reply->user->increment('points', 15);
        $discussion->user->increment('points', 5);

        // Notify the reply author about their best answer
        $reply->user->notify(new BestAnswerNotification($reply, $discussion));

        // Award "First Best Answer" badge if this is the user's first best answer
        $bestAnswersCount = Reply::where('user_id', $reply->user_id)
            ->where('best_answer', true)
            ->count();

        if ($bestAnswersCount === 1) {
            $firstBestAnswerBadge = Badge::where('slug', 'first-best-answer')->first();
            if ($firstBestAnswerBadge) {
                $reply->user->badges()->syncWithoutDetaching($firstBestAnswerBadge);
            }
        }

        return back()->with('success', 'Best answer marked!');
    }
}
