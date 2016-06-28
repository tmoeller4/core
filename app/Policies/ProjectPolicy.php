<?php

namespace Dias\Policies;

use Dias\Project;
use Dias\User;
use Dias\Role;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy extends CachedPolicy
{
    const TABLE = 'project_user';

    use HandlesAuthorization;

    /**
     * Intercept all checks
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before($user, $ability)
    {
        if ($user->isAdmin) {
            return true;
        }
    }

    /**
     * Determine if the given project can be accessed by the user.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return bool
     */
    public function access(User $user, Project $project)
    {
        return $this->remember("project-can-access-{$user->id}-{$project->id}", function () use ($user, $project) {
            return DB::table(self::TABLE)
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->exists();
        });
    }

    /**
     * Determine if the user can edit things in the given project.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return bool
     */
    public function editIn(User $user, Project $project)
    {
        return $this->remember("project-can-edit-in-{$user->id}-{$project->id}", function () use ($user, $project) {
            return DB::table(self::TABLE)
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->whereIn('project_role_id', [Role::$admin->id, Role::$editor->id])
                ->exists();
        });
    }

    /**
     * Determine if user can remove the given project member from the given project.
     *
     * @param  User  $user
     * @param  Project  $project
     * @param User $member
     * @return bool
     */
    public function removeMember(User $user, Project $project, User $member)
    {
        return $this->remember("project-can-remove-member-{$user->id}-{$project->id}-{$member->id}", function () use ($user, $project, $member) {

            $isMember = DB::table(self::TABLE)
                ->where('project_id', $project->id)
                ->where('user_id', $member->id)
                ->exists();

            if ($user->id === $member->id) {
                // each member is allowed to remove themselves
                return $isMember;
            } else {
                // admins can remove members other than themselves
                return $isMember && DB::table(self::TABLE)
                    ->where('project_id', $project->id)
                    ->where('user_id', $user->id)
                    ->where('project_role_id', Role::$admin->id)
                    ->exists();
            }
        });
    }

    /**
     * Determine if the given project can be updated by the user.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        return $this->remember("project-can-update-{$user->id}-{$project->id}", function () use ($user, $project) {
            return DB::table(self::TABLE)
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('project_role_id', Role::$admin->id)
                ->exists();
        });
    }

    /**
     * Determine if the given project can be deleted by the user.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return bool
     */
    public function destroy(User $user, Project $project)
    {
        return $this->update($user, $project);
    }
}