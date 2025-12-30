<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Discussion;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display search results.
     */
    public function __invoke(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect()->route('forum.index');
        }

        // Search discussions using Scout
        $discussions = Discussion::search($query)
            ->query(fn ($builder) => $builder->with(['user', 'channel'])
                ->withCount('replies'))
            ->paginate(20);

        // Get channels for sidebar
        $channels = Channel::withCount('discussions')->get();

        // Forum statistics
        $totalDiscussions = Discussion::count();
        $activeMembers = \App\Models\User::count();
        $todaysPosts = Discussion::whereDate('created_at', today())->count()
            + \App\Models\Reply::whereDate('created_at', today())->count();

        return view('search.results', compact(
            'query',
            'discussions',
            'channels',
            'totalDiscussions',
            'activeMembers',
            'todaysPosts'
        ));
    }
}
