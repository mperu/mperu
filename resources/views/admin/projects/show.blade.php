{{-- resources/views/admin/projects/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">
                Progetto #{{ $project->id }} (Back Office)
            </h2>

            <a href="{{ route('admin.projects.index') }}">
                <x-secondary-button>
                    ← Tutti i progetti
                </x-secondary-button>
            </a>
        </div>
    </x-slot>

    @php
        $comments = $project->comments ?? collect();
        $updates  = $project->updates ?? collect();

        $labelStatus = fn($s) => match($s) {
            'new' => 'Nuovo',
            'in_progress' => 'In lavorazione',
            'review' => 'In revisione',
            'delivered' => 'Consegnato',
            'closed' => 'Chiuso',
            default => $s,
        };

        $whoBadge = function($meta) {
            $by = data_get($meta, 'by');

            if (!$by) {
                if (data_get($meta, 'admin_id')) $by = 'admin';
                elseif (data_get($meta, 'client_id')) $by = 'client';
            }

            if ($by === 'admin') return ['ADMIN', 'bg-gray-900 text-white'];
            if ($by === 'client') return ['CLIENTE', 'bg-gray-200 text-gray-900'];
            return ['SYSTEM', 'bg-gray-100 text-gray-700'];
        };

        $prettyFileSize = function ($bytes) {
            $bytes = (int) ($bytes ?? 0);
            if ($bytes <= 0) return null;

            $kb = $bytes / 1024;
            if ($kb < 1024) return number_format($kb, 0, ',', '.') . ' KB';

            $mb = $kb / 1024;
            return number_format($mb, 1, ',', '.') . ' MB';
        };

        $renderUpdate = function($u) use ($labelStatus, $prettyFileSize) {
            $t = $u->type;
            $m = $u->meta ?? [];

            return match($t) {
                'project_created' =>
                    'Progetto creato' . (data_get($m,'order_id') ? ' (da ordine #' . data_get($m,'order_id') . ')' : ''),

                'comment_added' =>
                    'Messaggio inviato in chat' .
                    (data_get($m, 'excerpt') ? ' — “' . data_get($m, 'excerpt') . '”' : ''),

                'file_uploaded' =>
                    'File caricato' .
                    (data_get($m,'original_name') ? ': ' . data_get($m,'original_name') : '') .
                    (data_get($m,'size') ? ' (' . $prettyFileSize(data_get($m,'size')) . ')' : ''),

                'file_deleted' =>
                    'File eliminato' .
                    (data_get($m,'original_name') ? ': ' . data_get($m,'original_name') : ''),

                'admin_notes_updated' =>
                    'Note admin aggiornate' .
                    (data_get($m, 'notes_excerpt')
                        ? ' — “' . data_get($m, 'notes_excerpt') . '”'
                        : ' — (nota salvata)'
                    ),

                'status_changed' =>
                    'Status cambiato: ' . $labelStatus(data_get($m,'from')) . ' → ' . $labelStatus(data_get($m,'to')),

                'subdomain_updated' =>
                    'Subdomain aggiornato' . (data_get($m,'subdomain') ? ': ' . data_get($m,'subdomain') : ''),

                'snapshot_uploaded' =>
                    'Snapshot caricato' . (data_get($m,'filename') ? ': ' . data_get($m,'filename') : ''),

                default => $t,
            };
        };
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 rounded text-green-900">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-100 rounded text-red-900">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- INFO --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <div class="text-sm">
                    <strong>Cliente:</strong>
                    {{ $project->user->name ?? '—' }} ({{ $project->user->email ?? '—' }})
                </div>

                <div class="text-sm">
                    <strong>Status:</strong>
                    {{ $labelStatus($project->status) }}
                    <span class="text-gray-500 text-xs">({{ $project->status }})</span>
                </div>

                <div class="text-sm">
                    <strong>Subdomain:</strong> {{ $project->subdomain ?? '—' }}
                </div>

                <div class="text-sm">
                    <strong>Ordine:</strong>
                    @if($project->order)
                        #{{ $project->order->id }}
                        @if($project->order->quote)
                            — Preventivo #{{ $project->order->quote->id }}
                        @endif
                    @else
                        —
                    @endif
                </div>

                <div class="flex items-center gap-3 text-sm">
                    <strong>Snapshot:</strong>
                    @if($project->snapshot_path)
                        <span class="text-green-700 font-semibold">Presente</span>
                        <a href="{{ route('admin.projects.snapshot.download', $project) }}">
                            <x-primary-button>Scarica snapshot</x-primary-button>
                        </a>
                    @else
                        <span class="text-gray-500">—</span>
                    @endif
                </div>
            </div>

            {{-- NOTE ADMIN --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Note admin (private)</h3>
                    <span class="text-xs text-gray-500">Visibili solo in BO</span>
                </div>

                <form method="POST" action="{{ route('admin.projects.notes.update', $project) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <textarea
                        name="admin_notes"
                        rows="4"
                        class="w-full border rounded p-2 text-sm"
                        placeholder="Note interne..."
                    >{{ old('admin_notes', $project->admin_notes) }}</textarea>

                    <x-primary-button>Salva note</x-primary-button>
                </form>
            </div>

            {{-- UPDATE PROGETTO --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-lg">Aggiorna progetto</h3>

                <form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full border rounded p-2 text-sm">
                                @foreach(['new','in_progress','review','delivered','closed'] as $s)
                                    <option value="{{ $s }}" @selected($project->status === $s)>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-1">Subdomain</label>
                            <input
                                type="text"
                                name="subdomain"
                                value="{{ old('subdomain', $project->subdomain) }}"
                                class="w-full border rounded p-2 text-sm"
                                placeholder="es: cliente1.mperu.test"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Snapshot (zip/file – max 50MB)</label>
                        <input type="file" name="snapshot" class="block w-full text-sm">
                    </div>

                    <x-primary-button>Salva modifiche</x-primary-button>
                </form>
            </div>

            {{-- COMMENTI ADMIN --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg">Commenti (chat progetto)</h3>
                    <span class="text-xs text-gray-500">Cliente + Admin</span>
                </div>

                <form method="POST" action="{{ route('admin.projects.comments.store', $project) }}" class="space-y-3">
                    @csrf

                    <textarea
                        name="body"
                        rows="3"
                        class="w-full border rounded p-2 text-sm"
                        placeholder="Scrivi un messaggio al cliente..."
                        required
                    >{{ old('body') }}</textarea>

                    <x-primary-button>Invia come admin</x-primary-button>
                </form>

                <hr>

                @if($comments->count() === 0)
                    <div class="text-sm text-gray-600">Nessun messaggio.</div>
                @else
                    <div class="space-y-3">
                        @foreach($comments as $c)
                            <div class="border rounded p-3">
                                <div class="text-xs text-gray-500 flex items-center justify-between">
                                    <span>
                                        {{ $c->user?->name ?? '—' }}
                                        <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-semibold {{ $c->is_admin ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-900' }}">
                                            {{ $c->is_admin ? 'ADMIN' : 'CLIENTE' }}
                                        </span>
                                    </span>
                                    <span>{{ optional($c->created_at)->format('d/m/Y H:i') }}</span>
                                </div>

                                <div class="mt-2 text-sm text-gray-800 whitespace-pre-line">
                                    {{ $c->body }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- MATERIALI --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-lg">Materiali caricati dal cliente</h3>

                @if(($project->files?->count() ?? 0) === 0)
                    <div class="text-sm text-gray-600">Nessun file caricato.</div>
                @else
                    <table class="table-auto w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2">Nome</th>
                                <th class="py-2">Tipo</th>
                                <th class="py-2">Peso</th>
                                <th class="py-2">Data</th>
                                <th class="py-2 text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->files as $file)
                                <tr class="border-b">
                                    <td class="py-2">{{ $file->original_name }}</td>
                                    <td class="py-2">{{ $file->mime ?? '—' }}</td>
                                    <td class="py-2">
                                        @if($file->size)
                                            {{ number_format($file->size / 1024, 0, ',', '.') }} KB
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-2">{{ optional($file->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 text-right space-x-2">
                                        <a href="{{ Storage::disk('public')->url($file->path) }}" target="_blank" rel="noreferrer">
                                            <x-secondary-button>Apri</x-secondary-button>
                                        </a>
                                        <a href="{{ route('admin.projects.files.download', [$project, $file]) }}">
                                            <x-primary-button>Download</x-primary-button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- TIMELINE (UX PULITA) --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <h3 class="font-semibold text-lg">Timeline</h3>

                @if($updates->count() === 0)
                    <div class="text-sm text-gray-600">Nessun evento.</div>
                @else
                    <ul class="space-y-2 text-sm">
                        @foreach($updates as $u)
                            @php
                                $meta = $u->meta ?? [];
                                [$badgeText, $badgeClass] = $whoBadge($meta);

                                $byName =
                                    data_get($meta, 'admin_name')
                                    ?? data_get($meta, 'client_name')
                                    ?? ($badgeText === 'SYSTEM' ? 'System' : null);

                                $line = $renderUpdate($u);
                            @endphp

                            <li class="flex items-start justify-between gap-4 border rounded p-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </span>
                                        @if($byName)
                                            <span class="text-xs text-gray-500">da {{ $byName }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-2 text-gray-900">
                                        {{ $line }}
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 whitespace-nowrap">
                                    {{ optional($u->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>