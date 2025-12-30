<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Discussion;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
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
    public function store(Request $request, ImageService $imageService)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
        ]);

        $discussion = auth()->user()->discussions()->create([
            'channel_id' => $validated['channel_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.time(),
            'content' => $validated['content'],
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $disk = config('filesystems.images');
            foreach ($request->file('images') as $index => $image) {
                $imageService->setDisk($disk)->uploadAndOptimize(
                    $image,
                    $discussion,
                    null, // alt_text
                    1200, // max width
                    85    // quality
                );
            }
        }

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
        $discussion->load('images');

        return view('discussions.edit', compact('discussion', 'channels'));
    }

    /**
     * Update the specified discussion.
     */
    public function update(Request $request, Discussion $discussion, ImageService $imageService)
    {
        $this->authorize('update', $discussion);

        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'exists:images,id',
        ]);

        $discussion->update([
            'channel_id' => $validated['channel_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        // Handle image removals
        if ($request->has('removed_images')) {
            Image::whereIn('id', $request->removed_images)
                ->where('imageable_type', Discussion::class)
                ->where('imageable_id', $discussion->id)
                ->get()
                ->each(fn ($image) => $image->delete());
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $disk = config('filesystems.images');
            foreach ($request->file('images') as $index => $image) {
                $imageService->setDisk($disk)->uploadAndOptimize(
                    $image,
                    $discussion,
                    null, // alt_text
                    1200, // max width
                    85    // quality
                );
            }
        }

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
