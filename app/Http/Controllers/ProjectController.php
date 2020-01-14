<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Project list
     *
     * @param Auth $auth
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Auth $auth)
    {
        $projects = Auth::user()->projects;

        return view('projects.index', compact('projects'));
    }

    /**
     * Show project details
     *
     * @param Project $project
     * @param Auth $auth
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Project $project, Auth $auth)
    {
        if (Auth::user()->isNot($project->owner)) {
            abort(403);
        }

        return view('projects.show', compact('project'));
    }

    /**
     * Create Project
     *
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);

        $attributes = $request->only(['title', 'description']);

        Auth::user()->projects()->create($attributes);

        return redirect('/projects');
    }
}
