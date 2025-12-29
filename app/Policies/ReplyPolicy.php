<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;

class ReplyPolicy
{
    /**
     * Determine whether the user can update the reply.
     */
    public function update(User $user, Reply $reply): bool
    {
        return $user->id === $reply->user_id;
    }

    /**
     * Determine whether the user can delete the reply.
     */
    public function delete(User $user, Reply $reply): bool
    {
        return $user->id === $reply->user_id || $user->admin;
    }
}
