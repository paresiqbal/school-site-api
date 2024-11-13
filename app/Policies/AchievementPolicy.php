<?php

namespace App\Policies;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AchievementPolicy
{
    public function modified(User $user, Achievement $achievement): Response
    {
        return $user->id === $achievement->user_id
            ? Response::allow()
            : Response::deny('You do not own this achievement');
    }
}
