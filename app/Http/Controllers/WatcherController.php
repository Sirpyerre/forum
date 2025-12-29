<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class WatcherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle watching a discussion.
     */
    public function toggle(Discussion $discussion)
    {
        $user = auth()->user();

        // Check if user is already watching this discussion
        $existingWatcher = $discussion->watchers()->where('user_id', $user->id)->first();

        if ($existingWatcher) {
            // Unwatch
            $existingWatcher->delete();

            return back()->with('success', 'You are no longer watching this discussion.');
        } else {
            // Watch
            $discussion->watchers()->create([
                'user_id' => $user->id,
            ]);

            return back()->with('success', 'You are now watching this discussion!');
        }
    }
}
