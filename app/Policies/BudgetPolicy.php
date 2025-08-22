<?php

namespace Biigle\Policies;

use Biigle\Budget;
use Biigle\Role;
use Biigle\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept all checks.
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before($user, $ability)
    {
        if ($user->can('sudo')) {
            return true;
        }
    }

    /**
     * Determine if the given user can create budgets.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        // Users need at least editor role to create budgets
        return $user->role_id === Role::editorId() || $user->role_id === Role::adminId();
    }

    /**
     * Determine if the given budget can be accessed by the user.
     *
     * @param  User  $user
     * @param  Budget  $budget
     * @return bool
     */
    public function access(User $user, Budget $budget)
    {
        return $this->getProjectUserId($user->id, $budget->project_id) !== null;
    }

    /**
     * Determine if the given budget can be updated by the user.
     *
     * @param  User  $user
     * @param  Budget  $budget
     * @return bool
     */
    public function update(User $user, Budget $budget)
    {
        $projectUserId = $this->getProjectUserId($user->id, $budget->project_id);

        if ($projectUserId === null) {
            return false;
        }

        return $this->getProjectUserRole($projectUserId) >= Role::editorId();
    }

    /**
     * Determine if the given budget can be deleted by the user.
     *
     * @param  User  $user
     * @param  Budget  $budget
     * @return bool
     */
    public function destroy(User $user, Budget $budget)
    {
        $projectUserId = $this->getProjectUserId($user->id, $budget->project_id);

        if ($projectUserId === null) {
            return false;
        }

        // Only admins can delete budgets
        return $this->getProjectUserRole($projectUserId) >= Role::adminId();
    }
}