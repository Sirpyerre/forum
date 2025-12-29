<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Show the form for creating a new discussion.
     */
    public function create()
    {
        $channels = Channel::all();

        return view('discussions.create', compact('channels'));
    }

    /**
     * Store a newly created discussion.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $discussion = auth()->user()->discussions()->create([
            'channel_id' => $validated['channel_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . time(),
            'content' => $validated['content'],
        ]);

        // Award points for creating a discussion
        auth()->user()->increment('points', 5);

        return redirect()
            ->route('discussions.show', $discussion)
            ->with('success', 'Discussion created successfully!');
    }

    /**
     * Show the form for editing a discussion.
     */
    public function edit(Discussion $discussion)
    {
        $this->authorize('update', $discussion);

        $channels = Channel::all();

        return view('discussions.edit', compact('discussion', 'channels'));
    }

    /**
     * Update the specified discussion.
     */
    public function update(Request $request, Discussion $discussion)
    {
        $this->authorize('update', $discussion);

        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $discussion->update([
            'channel_id' => $validated['channel_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('discussions.show', $discussion)
            ->with('success', 'Discussion updated successfully!');
    }

    /**
     * Remove the specified discussion.
     */
    public function destroy(Discussion $discussion)
    {
        $this->authorize('delete', $discussion);

        $discussion->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Discussion deleted successfully!');
    }
}
