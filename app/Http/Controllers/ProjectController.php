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
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $projects = Auth::user()->projects;

        return view('projects.index', compact('projects'));
    }

    /**
     * Show project details
     *
     * @param Project $project
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.show', compact('project'));
    }

    /**
     * Create project form
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('projects.create');
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
            'notes' => 'min:3',
        ]);

        $attributes = $request->only(['title', 'description', 'notes']);

        $project = Auth::user()->projects()->create($attributes);

        return redirect($project->path());
    }

    /**
     * Update project
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $attributes = $request->only(['notes']);

        $project->update($attributes);

        return redirect($project->path());
    }
}
