<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
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
    public function only_the_owner_of_a_project_may_update_a_task()
    {
        $user = factory('App\User')->create();
        $this->signIn($user);

        $project = ProjectFactory::withTasks(1)->create();

        $attributes = [
            'body' => 'changed task',
            'completed' => true,
        ];

        $this->patch($project->tasks()->first()->path(), $attributes)
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', $attributes);
    }

    /**
     * @test
     */
    public function a_project_can_have_tasks()
    {
        $project = ProjectFactory::create();

        $attributes = [
            'body' => $this->faker->paragraph,
        ];

        $this->be($project->owner)
            ->post($project->path() . '/tasks', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['body']);
    }

    /**
     * @test
     */
    public function a_task_can_be_updated()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $attributes = [
            'body' => 'changed task',
            'completed' => true,
        ];

        $this->be($project->owner)
            ->patch($project->tasks()->first()->path(), $attributes);

        $this->assertDatabaseHas('tasks', $attributes);
    }

    /**
     * @test
     */
    public function a_task_requires_a_body()
    {
        $project = ProjectFactory::create();

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->be($project->owner)
            ->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors(['body']);
    }
}
