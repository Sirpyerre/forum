<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WatcherController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Debug route - REMOVE AFTER FIXING
Route::get('/debug-info', function () {
    return response()->json([
        'status' => 'Laravel is working!',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_key_set' => !empty(config('app.key')),
        'db_connection' => config('database.default'),
        'db_host' => config('database.connections.pgsql.host'),
        'db_database' => config('database.connections.pgsql.database'),
        'timezone' => config('app.timezone'),
        'url' => config('app.url'),
    ]);
});

// Forum routes
Route::get('/', [ForumController::class, 'index'])->name('forum.index');
Route::get('/search', SearchController::class)->name('search');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/channel/{channel:slug}', [ForumController::class, 'channel'])->name('channel.show');

Route::get('dashboard', function () {
    $user = auth()->user();

    // Load user data with relationships
    $user->load(['badges', 'discussions.channel', 'replies.discussion']);

    // Get watching discussions
    $watchingDiscussions = \App\Models\Discussion::whereHas('watchers', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->with(['user', 'channel'])->latest()->take(5)->get();

    // Get recent activity in user's discussions
    $recentActivity = \App\Models\Reply::whereIn('discussion_id', $user->discussions->pluck('id'))
        ->where('user_id', '!=', $user->id)
        ->with(['user', 'discussion'])
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact('user', 'watchingDiscussions', 'recentActivity'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Settings routes
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Discussion routes (authenticated)
    Route::get('/discussions/create', [DiscussionController::class, 'create'])->name('discussions.create');
    Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('/discussions/{discussion:slug}/edit', [DiscussionController::class, 'edit'])->name('discussions.edit');
    Route::patch('/discussions/{discussion}', [DiscussionController::class, 'update'])->name('discussions.update');
    Route::delete('/discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');

    // Reply routes
    Route::post('/discussions/{discussion}/replies', [ReplyController::class, 'store'])->name('replies.store');
    Route::patch('/replies/{reply}', [ReplyController::class, 'update'])->name('replies.update');
    Route::delete('/replies/{reply}', [ReplyController::class, 'destroy'])->name('replies.destroy');
    Route::post('/replies/{reply}/like', [ReplyController::class, 'like'])->name('replies.like');
    Route::post('/replies/{reply}/best-answer', [ReplyController::class, 'markBestAnswer'])->name('replies.best-answer');

    // Watcher routes
    Route::post('/discussions/{discussion}/watch', [WatcherController::class, 'toggle'])->name('discussions.watch');
});

// Discussion show route (public, must be after specific routes like 'create' and 'edit')
Route::get('/discussions/{discussion:slug}', [ForumController::class, 'show'])->name('discussions.show');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('channels', ChannelController::class)->except(['show']);
});
