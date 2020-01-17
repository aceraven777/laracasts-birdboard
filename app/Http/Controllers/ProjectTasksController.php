<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTasksController extends Controller
{
    public function store(Request $request, Auth $auth, Project $project)
    {
        if (Auth::user()->isNot($project->owner)) {
            abort(403);
        }

        $this->validate($request, [
            'body' => 'required',
        ]);

        $project->addTask($request->get('body'));

        return redirect($project->path());
    }
}
