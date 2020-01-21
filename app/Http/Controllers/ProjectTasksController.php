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

        $this->validate($request, [
            'body' => 'required',
        ]);

        $task->update([
            'body' => $request->input('body'),
            'completed' => $request->has('completed'),
        ]);

        return redirect($project->path());
    }
}
