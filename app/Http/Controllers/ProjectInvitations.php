<?php

namespace App\Http\Controllers;

use App\User;
use App\Project;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectInvitationRequest;

class ProjectInvitations extends Controller
{
    /**
     * Invite user to project
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(ProjectInvitationRequest $request, Project $project)
    {
        $user = User::where('email', $request->email)->first();

        $project->invite($user);

        return redirect($project->path());
    }
}
