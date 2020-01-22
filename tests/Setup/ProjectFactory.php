<?php

namespace Tests\Setup;

use App\Task;
use App\User;
use App\Project;

class ProjectFactory
{
    protected $user = null;

    protected $tasksCount = 0;

    /**
     * Set the user
     *
     * @param \App\User $user
     * @return ProjectFactory
     */
    public function ownedBy($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the number of tasks
     *
     * @param int $count
     * @return ProjectFactory
     */
    public function withTasks($count)
    {
        $this->tasksCount = $count;

        return $this;
    }

    /**
     * Create a project
     *
     * @return \App\Project
     */
    public function create()
    {
        $project = factory(Project::class)->create([
            'owner_id' => $this->user ?? factory(User::class),
        ]);

        factory(Task::class, $this->tasksCount)->create([
            'project_id' => $project->id,
        ]);

        return $project;
    }
}