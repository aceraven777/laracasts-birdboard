<?php

namespace Tests\Feature;

use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function creating_a_project()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activities);
        $this->assertEquals('created', $project->activities[0]->description);
    }

    /**
     * @test
     */
    public function updating_a_project()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'Changed']);
        $this->assertCount(2, $project->activities);
        $this->assertEquals('updated', $project->activities->last()->description);
    }

    /**
     * @test
     */
    public function creating_a_new_task()
    {
        $project = ProjectFactory::create();

        $project->addTask('Some task');

        $this->assertCount(2, $project->activities);
        $this->assertEquals('created_task', $project->activities->last()->description);
    }

    /**
     * @test
     */
    public function toggle_completing_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        // Complete a task
        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'foobar',
                'completed' => true,
            ]);

        $this->assertCount(3, $project->activities);
        $this->assertEquals('completed_task', $project->activities->last()->description);

        // Incomplete a task
        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'foobar',
                'completed' => false,
            ]);

        $project->refresh();

        $this->assertCount(4, $project->activities);
        $this->assertEquals('incompleted_task', $project->activities->last()->description);
    }

    /**
     * @test
     */
    public function deleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $project->tasks[0]->delete();

        $this->assertCount(3, $project->activities);
        $this->assertEquals('deleted_task', $project->activities->last()->description);
    }
}
