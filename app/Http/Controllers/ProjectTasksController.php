<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectTasksController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        $project->addTask($request->get('body'));

        return redirect($project->path());
    }
}
