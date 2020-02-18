<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_projects()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    /** @test */
    public function a_user_has_accessible_projects()
    {
        $john = $this->signIn();
        $project = ProjectFactory::ownedBy($john)->create();

        $this->assertTrue($john->accessibleProjects()->contains($project));

        $sally = factory(User::class)->create();
        $nick = factory(User::class)->create();
        
        $sallyProject = ProjectFactory::ownedBy($sally)->create();

        $sallyProject->invite($nick);
        
        $this->assertFalse($john->accessibleProjects()->contains($sallyProject));
        
        $sallyProject->invite($john);
        
        $this->assertTrue($john->accessibleProjects()->contains($sallyProject));
    }
}
