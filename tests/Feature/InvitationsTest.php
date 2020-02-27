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
    public function guest_cannot_invite_a_user()
    {
        $project = ProjectFactory::create();

        $userToInvite = factory(User::class)->create();
        
        $this->post($project->path() . '/invitations', [
                'email' => $userToInvite->email
            ])
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function non_owners_may_not_invite_users()
    {
        $project = ProjectFactory::create();

        $user = factory(User::class)->create();
        $userToInvite = factory(User::class)->create();

        $assertInvitationForbidden = function () use ($project, $user, $userToInvite) {
            $this->be($user)
                ->post($project->path() . '/invitations', [
                    'email' => $userToInvite->email
                ])
                ->assertStatus(403);
        };

        $assertInvitationForbidden();

        $project->invite($user);

        $assertInvitationForbidden();
    }

    /**
     * @test
     */
    public function a_project_owner_can_invite_a_user()
    {
        $this->withoutExceptionHandling();
        
        $project = ProjectFactory::create();

        $userToInvite = factory(User::class)->create();
        
        $this->be($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => $userToInvite->email
            ])
            ->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));
    }

    /**
     * @test
     */
    public function the_email_address_must_be_a_valid_birdboard_account()
    {
        $project = ProjectFactory::create();

        $this->be($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => 'notauser@example.com'
            ])
            ->assertSessionHasErrors([
                'email' => 'The user you are inviting must have a Birdboard account.'
            ], null, 'invitations');
    }

    /**
     * @test
     */
    public function invited_users_may_update_project_details()
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
