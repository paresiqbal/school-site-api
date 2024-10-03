<?php

namespace App\Policies;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AgendaPolicy
{
    public function modified(User $user, Agenda $agenda): Response
    {
        return $user->id === $agenda->user_id
            ? Response::allow()
            : Response::deny('You do not own this news');
    }
}
