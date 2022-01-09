<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    //
    public function is_owner(User $user, User $model)
    {
        // die($user);
        return $user->is_admin == 1 || $user->id === $model->id;
    }

    public function is_admin(User $user)
    {
        return $user->is_admin === 1;
    }
}
