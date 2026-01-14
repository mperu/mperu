<?php

namespace App\Http\Controllers;

use App\Models\ProjectFile;

class ProjectFileController extends Controller
{
    public function index()
    {
        $files = ProjectFile::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('uploads.index', compact('files'));
    }
}