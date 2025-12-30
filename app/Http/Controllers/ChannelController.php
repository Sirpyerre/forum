<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    /**
     * Display a listing of channels.
     */
    public function index()
    {
        $channels = Channel::withCount('discussions')->get();

        return view('admin.channels.index', compact('channels'));
    }

    /**
     * Show the form for creating a new channel.
     */
    public function create()
    {
        return view('admin.channels.create');
    }

    /**
     * Store a newly created channel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:channels,title',
            'description' => 'nullable|string|max:500',
        ]);

        Channel::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('admin.channels.index')
            ->with('success', 'Channel created successfully!');
    }

    /**
     * Show the form for editing a channel.
     */
    public function edit(Channel $channel)
    {
        return view('admin.channels.edit', compact('channel'));
    }

    /**
     * Update the specified channel.
     */
    public function update(Request $request, Channel $channel)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:channels,title,'.$channel->id,
            'description' => 'nullable|string|max:500',
        ]);

        $channel->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('admin.channels.index')
            ->with('success', 'Channel updated successfully!');
    }

    /**
     * Remove the specified channel.
     */
    public function destroy(Channel $channel)
    {
        $channel->delete();

        return redirect()
            ->route('admin.channels.index')
            ->with('success', 'Channel deleted successfully!');
    }
}
