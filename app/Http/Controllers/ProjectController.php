<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Project list
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $projects = Project::all();

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

        Project::create($request->only(['title', 'description']));

        return redirect('/projects');
    }
}
