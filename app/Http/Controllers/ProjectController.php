<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }
}