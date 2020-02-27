<?php

namespace App\Policies;

use App\User;
use App\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Can user manage a project
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function manage(User $user, Project $project)
    {
        return $user->is($project->owner);
    }

    /**
     * Can user update a project
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        return $user->is($project->owner) || $project->members->contains($user);
    }
}
