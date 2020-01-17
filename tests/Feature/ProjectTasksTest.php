<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    /**
     * @test
     */
    public function only_the_owner_of_a_project_may_add_tasks()
    {
        $user = factory('App\User')->create();
        $this->signIn($user);

        $project = factory(Project::class)->create();

        $attributes = [
            'body' => $this->faker->paragraph,
        ];

        $this->post($project->path() . '/tasks', $attributes)
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => $attributes['body']]);
    }

    /**
     * @test
     */
    public function a_project_can_have_tasks()
    {
        $user = factory('App\User')->create();
        $this->signIn($user);

        $project_raw = factory(Project::class)->raw();
        $project = $user->projects()->create($project_raw);

        $attributes = [
            'body' => $this->faker->paragraph,
        ];

        $this->post($project->path() . '/tasks', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['body']);
    }

    /**
     * @test
     */
    public function a_task_requires_a_body()
    {
        $user = factory('App\User')->create();
        $this->signIn($user);

        $project_raw = factory(Project::class)->raw();
        $project = $user->projects()->create($project_raw);

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors(['body']);
    }
}
