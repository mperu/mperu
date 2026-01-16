<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->where('user_id', auth()->id())
            ->with(['order.quote', 'files'])
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'order.quote',
            'files' => fn ($q) => $q->latest(),
            'comments.user',
            'updates' => fn ($q) => $q->latest(),
        ]);

        return view('projects.show', compact('project'));
    }

    public function storeComment(Request $request, Project $project)
    {
        $this->authorize('comment', $project);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = $project->comments()->create([
            'user_id' => auth()->id(),
            'is_admin' => false,
            'body' => $data['body'],
        ]);

        $excerpt = Str::of($comment->body)
            ->replace(["\r\n", "\n", "\r"], ' ')
            ->squish()
            ->limit(160)
            ->toString();

        $project->updates()->create([
            'type' => 'comment_added',
            'meta' => [
                'by' => 'client',
                'client_id' => auth()->id(),
                'client_name' => auth()->user()?->name,
                'comment_id' => $comment->id,
                'excerpt' => $excerpt,
            ],
        ]);

        return back()->with('status', 'Commento inviato ✅');
    }

    /**
     * FE: download snapshot (solo se consegnato/chiuso + snapshot presente)
     */
    public function downloadSnapshot(Project $project)
    {
        $this->authorize('view', $project);

        // sicurezza: il cliente scarica solo a consegna/chiusura
        if (!in_array($project->status, ['delivered', 'closed'], true)) {
            abort(403);
        }

        if (!$project->snapshot_path || !Storage::disk('public')->exists($project->snapshot_path)) {
            abort(404);
        }

        $ext = pathinfo($project->snapshot_path, PATHINFO_EXTENSION) ?: 'zip';

        return Storage::disk('public')->download(
            $project->snapshot_path,
            "snapshot-project-{$project->id}.{$ext}"
        );
    }
}