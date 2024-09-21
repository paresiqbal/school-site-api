<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{

    public function modified(User $user, News $news): Response
    {
        return $user->id === $news->user_id
            ? Response::allow()
            : Response::deny('You do not own this news');
    }
}
