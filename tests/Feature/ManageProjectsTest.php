<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function guests_cannot_manage_projects()
    {
        $project = factory('App\Project')->create();
        $project_raw = factory('App\Project')->raw();

        // Try to view projects
        $this->get('/projects')
            ->assertRedirect('login');

        // Try to view a single project
        $this->get($project->path())
            ->assertRedirect('login');

        // Try to view the create project page
        $this->get('/projects/create')
            ->assertRedirect('login');

        // Try to create a project
        $this->post('/projects', $project_raw)
            ->assertRedirect('login');

        // Try to view edit project form
        $this->get($project->path() . '/edit')
            ->assertRedirect('login');

        // Try to update a project
        $this->patch($project->path(), $project_raw)
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function a_user_can_create_a_project()
    {
        $this->signIn();

        // Check if page to create a project exists
        $this->get('/projects/create')->assertStatus(200);

        $attributes = factory(Project::class)->raw();

        $this->followingRedirects()
            ->post('/projects', $attributes)
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /**
     * @test
     */
    public function tasks_can_be_included_as_part_of_a_new_project_creation()
    {
        $this->signIn();

        $attributes = factory(Project::class)->raw();

        $task1 = 'Task 1';
        $task2 = 'Task 2';

        $attributes['tasks'] = [
            ['body' => $task1],
            ['body' => $task2],
        ];

        $this->followingRedirects()
            ->post('/projects', $attributes);

        $this->assertCount(2, Project::latest()->first()->tasks);
    }

    /**
     * @test
     */
    public function a_user_can_see_all_projects_they_have_been_invited_to_on_their_dashboard()
    {
        $user = $this->signIn();

        $project = ProjectFactory::create();
        $project->invite($user);

        $this->get('/projects')->assertSee($project->title);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_project()
    {
        $project = ProjectFactory::create();

        $project_id = $project->id;

        $this->be($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', [
            'id' => $project_id
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_project()
    {
        $project = ProjectFactory::create();

        $attributes = [
            'title' => 'title changed',
            'description' => 'description changed',
            'notes' => 'notes changed',
        ];

        $this->be($project->owner)
            ->patch($project->path(), $attributes)
            ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertOk();

        $attributes['id'] = $project->id;

        $this->assertDatabaseHas('projects', $attributes);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_projects_general_notes()
    {
        $project = ProjectFactory::create();

        $attributes = [
            'notes' => 'notes changed',
        ];

        $this->be($project->owner)
            ->patch($project->path(), $attributes);

        $this->assertDatabaseHas('projects', $attributes);
    }

    /**
     * @test
     */
    public function a_user_can_view_their_project()
    {
        $project = ProjectFactory::create();

        $this->be($project->owner)
            ->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /**
     * @test
     */
    public function an_authenticated_user_cannot_view_the_projects_of_others()
    {
        $this->signIn();
        $project = factory('App\Project')->create();

        $this->get($project->path())
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function an_authenticated_user_cannot_update_the_projects_of_others()
    {
        $this->signIn();
        $project = factory('App\Project')->create();

        $this->patch($project->path())
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function unauthorized_users_cannot_delete_the_projects()
    {
        $project = factory('App\Project')->create();

        // Try to delete a project
        $this->delete($project->path())
            ->assertRedirect('login');

        $user = $this->signIn();

        $this->delete($project->path())
            ->assertStatus(403);

        $project->invite($user);

        $this->delete($project->path())
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        $attributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $attributes)
            ->assertSessionHasErrors(['title']);
    }

    /**
     * @test
     */
    public function a_project_requires_a_description()
    {
        $this->signIn();
        
        $attributes = factory('App\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)
            ->assertSessionHasErrors(['description']);
    }
}
