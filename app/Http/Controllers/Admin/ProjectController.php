<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // BO list: usa viewAny perché non hai un Project concreto
        $this->authorize('viewAny', Project::class);

        $status = $request->query('status');

        $projects = Project::query()
            ->with([
                'user:id,name,email',
                'order:id,user_id,status,total_amount,deposit_amount,balance_amount,created_at',
            ])
            ->withCount('files')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.index', compact('projects', 'status'));
    }

    public function show(Project $project)
    {
        $this->authorize('manage', $project);

        $project->load([
            'user:id,name,email',
            'order.quote',
            'files' => fn ($q) => $q->latest(),
            'comments.user' => fn ($q) => $q->latest(),
            'updates' => fn ($q) => $q->latest(),
        ]);

        return view('admin.projects.show', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('manage', $project);

        $validated = $request->validate([
            'status'    => ['required', 'string', 'in:new,in_progress,review,delivered,closed'],
            'subdomain' => ['nullable', 'string', 'max:255'],
            'snapshot'  => ['nullable', 'file', 'max:51200'], // 50MB
        ]);

        $oldStatus = $project->status;
        $oldSub    = $project->subdomain;

        $project->status = $validated['status'];
        $project->subdomain = $validated['subdomain'] ?? null;

        // salva campi base
        $project->save();

        // timeline: status changed
        if ($oldStatus !== $project->status) {
            $project->updates()->create([
                'type' => 'status_changed',
                'meta' => [
                    'by' => 'admin',
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()?->name,
                    'from' => $oldStatus,
                    'to' => $project->status,
                ],
            ]);
        }

        // timeline: subdomain updated
        if (($oldSub ?? '') !== ($project->subdomain ?? '')) {
            $project->updates()->create([
                'type' => 'subdomain_updated',
                'meta' => [
                    'by' => 'admin',
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()?->name,
                    'subdomain' => $project->subdomain,
                ],
            ]);
        }

        // snapshot upload (+ timeline)
        if ($request->hasFile('snapshot')) {
            $file = $request->file('snapshot');
            $path = $file->store("project-snapshots/{$project->id}", 'public');

            if ($project->snapshot_path && Storage::disk('public')->exists($project->snapshot_path)) {
                Storage::disk('public')->delete($project->snapshot_path);
            }

            $project->snapshot_path = $path;
            $project->save();

            $project->updates()->create([
                'type' => 'snapshot_uploaded',
                'meta' => [
                    'by' => 'admin',
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()?->name,
                    'filename' => $file->getClientOriginalName(),
                ],
            ]);
        }

        return back()->with('status', 'Progetto aggiornato ✅');
    }

    /**
     * NOTE ADMIN (private) + timeline
     */
    public function updateNotes(Request $request, Project $project)
    {
        $this->authorize('manage', $project);

        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:20000'],
        ]);

        $project->admin_notes = $data['admin_notes'] ?? null;
        $project->save();

        $excerpt = Str::of($project->admin_notes ?? '')
            ->replace(["\r\n", "\n", "\r"], ' ')
            ->squish()
            ->limit(140)
            ->toString();

        $project->updates()->create([
            'type' => 'admin_notes_updated',
            'meta' => [
                'by' => 'admin',
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()?->name,
                'notes_excerpt' => $excerpt ?: null,
            ],
        ]);

        return back()->with('status', 'Note salvate ✅');
    }

    /**
     * Commento inviato come ADMIN + timeline
     */
    public function storeComment(Request $request, Project $project)
    {
        $this->authorize('manage', $project);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = $project->comments()->create([
            'user_id' => auth()->id(),
            'is_admin' => true,
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
                'by' => 'admin',
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()?->name,
                'comment_id' => $comment->id,
                'excerpt' => $excerpt,
            ],
        ]);

        return back()->with('status', 'Messaggio inviato ✅');
    }

    public function downloadFile(Project $project, ProjectFile $file)
    {
        $this->authorize('manage', $project);

        abort_unless((int) $file->project_id === (int) $project->id, 404);

        if (!Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $file->path,
            $file->original_name ?: basename($file->path)
        );
    }

    public function downloadSnapshot(Project $project)
    {
        $this->authorize('manage', $project);

        if (!$project->snapshot_path || !Storage::disk('public')->exists($project->snapshot_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $project->snapshot_path,
            "snapshot-project-{$project->id}." . pathinfo($project->snapshot_path, PATHINFO_EXTENSION)
        );
    }
}