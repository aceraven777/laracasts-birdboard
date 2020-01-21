<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
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

        // Try to update a project
        $this->patch($project->path(), $project_raw)
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        // Check if page to create a project exists
        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => $this->faker->paragraph,
        ];

        $response = $this->post('/projects', $attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_project()
    {
        $this->withoutExceptionHandling();

        $user = factory('App\User')->create();
        $project = factory('App\Project')->create(['owner_id' => $user->id]);

        $this->signIn($user);
        
        $changed_notes_text = 'Changed';

        $this->patch($project->path(), [
            'notes' => $changed_notes_text,
        ])->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'notes' => $changed_notes_text,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_view_their_project()
    {
        $this->withoutExceptionHandling();

        $user = factory('App\User')->create();
        $project = factory('App\Project')->create(['owner_id' => $user->id]);

        $this->signIn($user);

        $this->get($project->path())
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
