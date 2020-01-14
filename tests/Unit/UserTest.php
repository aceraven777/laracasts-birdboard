<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_has_projects()
    {
        $user = factory('App\User')->create();
        // $project1 = factory('App\Project')->create(['owner_id' => $user->id]);
        // $project2 = factory('App\Project')->create(['owner_id' => $user->id]);

        $this->assertInstanceOf(Collection::class, $user->projects);

        // $this->assertContains($project1->toArray(), $user->projects->toArray());
        // $this->assertDatabaseHas('projects', $user->toArray());
    }
}
