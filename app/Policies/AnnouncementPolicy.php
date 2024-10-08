<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnnouncementPolicy
{
    public function modified(User $user, Announcement $announcement): Response
    {
        return $user->id === $announcement->user_id
            ? Response::allow()
            : Response::deny('You do not own this announcement.');
    }
}
