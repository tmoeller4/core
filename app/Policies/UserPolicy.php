<?php

namespace Biigle\Policies;

use Biigle\User;
use Biigle\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create users.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->role_id === Role::$admin->id;
    }

    /**
     * Determine if the given user can update the user.
     *
     * @param  User  $user
     * @param  User  $updateUser
     * @return bool
     */
    public function update(User $user, User $updateUser)
    {
        return $user->id === $updateUser->id || $user->role_id === Role::$admin->id;
    }

    /**
     * Determine if the given user can delete the user.
     *
     * @param  User  $user
     * @param  User  $destroyUser
     * @return bool
     */
    public function destroy(User $user, User $destroyUser)
    {
        return $this->update($user, $destroyUser);
    }
}
