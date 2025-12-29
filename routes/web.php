<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\WatcherController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Forum routes
Route::get('/', [ForumController::class, 'index'])->name('forum.index');
Route::get('/channel/{channel:slug}', [ForumController::class, 'channel'])->name('channel.show');
Route::get('/discussions/{discussion:slug}', [ForumController::class, 'show'])->name('discussions.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

    // Discussion routes
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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('channels', ChannelController::class)->except(['show']);
});
