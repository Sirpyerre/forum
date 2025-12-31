<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Discussion;

class ForumController extends Controller
{
    /**
     * Display the forum home page with all discussions.
     */
    public function index()
    {
        $discussions = Discussion::with(['user', 'channel', 'replies'])
            ->withCount('replies')
            ->latest()
            ->paginate(20);

        $channels = Channel::withCount('discussions')->get();

        // Forum statistics
        $totalDiscussions = Discussion::count();
        $activeMembers = \App\Models\User::count();
        $todaysPosts = Discussion::whereDate('created_at', today())->count()
            + \App\Models\Reply::whereDate('created_at', today())->count();

        return view('forum.index', compact('discussions', 'channels', 'totalDiscussions', 'activeMembers', 'todaysPosts'));
    }

    /**
     * Display discussions for a specific channel.
     */
    public function channel(Channel $channel)
    {
        $discussions = $channel->discussions()
            ->with(['user', 'replies'])
            ->withCount('replies')
            ->latest()
            ->paginate(20);

        $channels = Channel::withCount('discussions')->get();

        // Forum statistics
        $totalDiscussions = Discussion::count();
        $activeMembers = \App\Models\User::count();
        $todaysPosts = Discussion::whereDate('created_at', today())->count()
            + \App\Models\Reply::whereDate('created_at', today())->count();

        return view('forum.channel', compact('channel', 'discussions', 'channels', 'totalDiscussions', 'activeMembers', 'todaysPosts'));
    }

    /**
     * Display a single discussion with its replies.
     */
    public function show(Discussion $discussion)
    {
        // Increment view count
        $discussion->increment('views');

        // Load discussion with relationships
        $discussion->load(['user.badges', 'channel', 'images', 'replies.user.badges', 'replies.likes', 'replies.images']);

        // Check if current user is watching this discussion
        $isWatching = auth()->check()
            ? $discussion->watchers()->where('user_id', auth()->id())->exists()
            : false;

        return view('forum.show', compact('discussion', 'isWatching'));
    }
}
