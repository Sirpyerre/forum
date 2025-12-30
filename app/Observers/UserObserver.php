<?php

namespace App\Observers;

use App\Models\Badge;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Award "Newcomer" badge to new users
        $newcomerBadge = Badge::where('slug', 'newcomer')->first();
        if ($newcomerBadge) {
            $user->badges()->syncWithoutDetaching($newcomerBadge);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if points were updated
        if ($user->wasChanged('points')) {
            $this->awardPointsBasedBadges($user);
        }
    }

    /**
     * Award badges based on points milestones.
     */
    private function awardPointsBasedBadges(User $user): void
    {
        $pointsBadges = Badge::where('points_required', '>', 0)
            ->where('points_required', '<=', $user->points)
            ->get();

        foreach ($pointsBadges as $badge) {
            // Only attach if not already earned
            $user->badges()->syncWithoutDetaching($badge);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
