<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectFileController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('uploadFiles', $project);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB
        ]);

        $uploaded = $validated['file'];

        $path = $uploaded->store("project-files/{$project->id}", 'public');

        $file = ProjectFile::create([
            'project_id'    => $project->id,
            'user_id'       => auth()->id(),
            'original_name' => $uploaded->getClientOriginalName(),
            'path'          => $path,
            'mime'          => $uploaded->getClientMimeType(),
            'size'          => (int) $uploaded->getSize(),
        ]);

        // Timeline: file_uploaded
        $project->updates()->create([
            'type' => 'file_uploaded',
            'meta' => [
                'by' => 'client',
                'client_id' => auth()->id(),
                'client_name' => auth()->user()?->name,
                'file_id' => $file->id,
                'original_name' => $file->original_name,
                'mime' => $file->mime,
                'size' => $file->size,
            ],
        ]);

        return back()->with('status', 'File caricato ✅');
    }

    public function download(Project $project, ProjectFile $file)
    {
        $this->authorize('view', $project);
        abort_unless((int) $file->project_id === (int) $project->id, 404);

        if (!Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $file->path,
            $file->original_name ?: basename($file->path)
        );
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        $this->authorize('uploadFiles', $project);
        abort_unless((int) $file->project_id === (int) $project->id, 404);

        $meta = [
            'by' => 'client',
            'client_id' => auth()->id(),
            'client_name' => auth()->user()?->name,
            'file_id' => $file->id,
            'original_name' => $file->original_name,
            'mime' => $file->mime,
            'size' => $file->size,
        ];

        if (!empty($file->path) && Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        $file->delete();

        // Timeline: file_deleted
        $project->updates()->create([
            'type' => 'file_deleted',
            'meta' => $meta,
        ]);

        return back()->with('status', 'File eliminato ✅');
    }
}