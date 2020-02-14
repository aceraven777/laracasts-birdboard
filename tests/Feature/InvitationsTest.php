<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_project_can_invite_a_user()
    {
        $project = ProjectFactory::create();

        $newUser = factory(User::class)->create();
        $project->invite($newUser);

        $this->be($newUser);
        $taskAttributes = ['body' => 'Foo task'];
        $this->post(action('ProjectTasksController@store', $project), $taskAttributes);

        $this->assertDatabaseHas('tasks', $taskAttributes);
    }
}
