<?php

namespace App\Http\Controllers;

use App\Task;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTasksController extends Controller
{
    /**
     * Insert task
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $this->validate($request, [
            'body' => 'required',
        ]);

        $project->addTask($request->get('body'));

        return redirect($project->path());
    }

    /**
     * Update a task
     *
     * @param Request $request
     * @param Project $project
     * @param Task $task
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $task->project);

        $attributes = $request->validate([
            'body' => 'required',
        ]);

        $task->update($attributes);

        $this->markTaskCompletion($task, $request);

        return redirect($project->path());
    }

    /**
     * Mark task as complete / incomplete
     *
     * @param Task $task
     * @param Request $request
     * @return void
     */
    private function markTaskCompletion($task, $request)
    {
        $method = $request->input('completed') ? 'complete' : 'incomplete';

        $task->$method();
    }
}
