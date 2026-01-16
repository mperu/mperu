{{-- resources/views/projects/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Progetto #{{ $project->id }}</h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}">
                    <x-secondary-button>← Dashboard</x-secondary-button>
                </a>

                <a href="{{ route('projects.index') }}">
                    <x-secondary-button>Progetti</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $comments = $project->comments ?? collect();
        $updates = $project->updates ?? collect();

        // FE: non mostriamo eventi admin-only
        $feUpdates = $updates->reject(fn($u) => in_array($u->type, ['admin_notes_updated'], true));

        $labelStatus = fn($s) => match ($s) {
            'new' => 'Nuovo',
            'in_progress' => 'In lavorazione',
            'review' => 'In revisione',
            'delivered' => 'Consegnato',
            'closed' => 'Chiuso',
            default => $s,
        };

        $whoBadge = function ($meta) {
            $by = data_get($meta, 'by');

            // fallback vecchi record
            if (!$by) {
                if (data_get($meta, 'admin_id')) {
                    $by = 'admin';
                } elseif (data_get($meta, 'client_id')) {
                    $by = 'client';
                }
            }

            if ($by === 'admin') {
                return ['ADMIN', 'bg-gray-900 text-white'];
            }
            if ($by === 'client') {
                return ['TU', 'bg-gray-200 text-gray-900'];
            }
            return ['SYSTEM', 'bg-gray-100 text-gray-700'];
        };

        $prettyFileSize = function ($bytes) {
            $bytes = (int) ($bytes ?? 0);
            if ($bytes <= 0) {
                return null;
            }

            $kb = $bytes / 1024;
            if ($kb < 1024) {
                return number_format($kb, 0, ',', '.') . ' KB';
            }

            $mb = $kb / 1024;
            return number_format($mb, 1, ',', '.') . ' MB';
        };

        $renderUpdate = function ($u) use ($labelStatus, $prettyFileSize) {
            $t = $u->type;
            $m = $u->meta ?? [];

            return match ($t) {
                'project_created' => 'Progetto creato' .
                    (data_get($m, 'order_id') ? ' (da ordine #' . data_get($m, 'order_id') . ')' : ''),

                'comment_added' => 'Messaggio in chat' .
                    (data_get($m, 'excerpt') ? ' — “' . data_get($m, 'excerpt') . '”' : ''),

                'file_uploaded' => 'File caricato' .
                    (data_get($m, 'original_name') ? ': ' . data_get($m, 'original_name') : '') .
                    (data_get($m, 'size') ? ' (' . $prettyFileSize(data_get($m, 'size')) . ')' : ''),

                'file_deleted' => 'File eliminato' .
                    (data_get($m, 'original_name') ? ': ' . data_get($m, 'original_name') : ''),

                'status_changed' => 'Status aggiornato: ' .
                    $labelStatus(data_get($m, 'from')) .
                    ' → ' .
                    $labelStatus(data_get($m, 'to')),

                'subdomain_updated' => 'Subdomain aggiornato' .
                    (data_get($m, 'subdomain') ? ': ' . data_get($m, 'subdomain') : ''),

                // dal BO: per FE lo facciamo “umano” ma senza dettagli
                'snapshot_uploaded' => 'Snapshot pubblicato',

                default => $t,
            };
        };
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if (session('status'))
                <div class="p-4 bg-green-100 rounded text-green-900">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errori --}}
            @if ($errors->any())
                <div class="p-4 bg-red-100 rounded text-red-900">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info progetto --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <div class="text-sm">
                    <strong>Status:</strong> {{ $labelStatus($project->status ?? 'new') }}
                    <span class="text-gray-500 text-xs">({{ $project->status ?? 'new' }})</span>
                </div>

                <div class="text-sm">
                    <strong>Ordine:</strong>
                    @if (optional($project->order)->id)
                        <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('orders.show', $project->order) }}">
                            Ordine #{{ $project->order->id }}
                        </a>

                        @if (optional($project->order->quote)->id)
                            <span class="text-gray-500">—</span>
                            <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('quotes.show', $project->order->quote) }}">
                                Preventivo #{{ $project->order->quote->id }}
                            </a>
                        @endif
                    @else
                        —
                    @endif
                </div>

                <div class="text-sm">
                    <strong>Subdomain:</strong> {{ $project->subdomain ?? '—' }}
                </div>
            </div>

            {{-- Snapshot FE --}}
            @if (in_array($project->status, ['delivered', 'closed'], true) && $project->snapshot_path)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold">Snapshot pronto</h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Puoi scaricare i file di consegna del progetto.
                            </p>
                        </div>

                        <a href="{{ route('projects.snapshot.download', $project) }}">
                            <x-primary-button>Scarica snapshot</x-primary-button>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Upload materiali --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-semibold">Upload materiali</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Carica loghi, testi, immagini, PDF ecc.
                            I file finiscono in <strong>storage/app/public/project-files/{{ $project->id }}</strong>.
                        </p>
                    </div>
                    <span class="text-xs text-gray-500 whitespace-nowrap">Max 10MB</span>
                </div>

                @can('uploadFiles', $project)
                    <form method="POST" action="{{ route('project-files.store', $project) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf

                        <input type="file" name="file" class="block w-full text-sm text-gray-700" required>

                        <x-primary-button>
                            Carica file
                        </x-primary-button>
                    </form>
                @else
                    <div class="p-3 rounded bg-gray-100 text-gray-700 text-sm">
                        Upload disabilitato: progetto chiuso.
                    </div>
                @endcan

                <hr>

                <h4 class="font-semibold text-sm">File caricati</h4>

                @if (($project->files?->count() ?? 0) === 0)
                    <div class="text-sm text-gray-600">Nessun file caricato.</div>
                @else
                    <div class="overflow-auto">
                        <table class="table-auto w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">Nome</th>
                                    <th class="py-2">Tipo</th>
                                    <th class="py-2">Peso</th>
                                    <th class="py-2">Caricato</th>
                                    <th class="py-2 text-right">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($project->files as $file)
                                    @php
                                        $publicUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($file->path);
                                    @endphp

                                    <tr class="border-b">
                                        <td class="py-2">{{ $file->original_name }}</td>
                                        <td class="py-2 text-gray-600">{{ $file->mime ?? '—' }}</td>
                                        <td class="py-2 text-gray-600">
                                            {{ $file->size ? number_format($file->size / 1024, 0, ',', '.') . ' KB' : '—' }}
                                        </td>
                                        <td class="py-2 text-gray-600">
                                            {{ optional($file->created_at)->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td class="py-2 text-right space-x-2">
                                            <a href="{{ $publicUrl }}" target="_blank" rel="noreferrer">
                                                <x-secondary-button>Apri</x-secondary-button>
                                            </a>

                                            <a href="{{ route('project-files.download', [$project, $file]) }}">
                                                <x-secondary-button>Download</x-secondary-button>
                                            </a>

                                            @can('uploadFiles', $project)
                                                <form method="POST" action="{{ route('project-files.destroy', [$project, $file]) }}" class="inline"
                                                      onsubmit="return confirm('Vuoi eliminare questo file?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit">Elimina</x-danger-button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Commenti FE --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Commenti</h3>
                    <span class="text-xs text-gray-500">Chat progetto</span>
                </div>

                @can('comment', $project)
                    <form method="POST" action="{{ route('projects.comments.store', $project) }}" class="space-y-3">
                        @csrf

                        <textarea name="body" rows="3" class="w-full border rounded p-2 text-sm" placeholder="Scrivi un messaggio..." required>{{ old('body') }}</textarea>

                        <x-primary-button>Invia</x-primary-button>
                    </form>
                @else
                    <div class="p-3 rounded bg-gray-100 text-gray-700 text-sm">
                        Commenti disabilitati: progetto chiuso.
                    </div>
                @endcan

                <hr>

                @if ($comments->count() === 0)
                    <div class="text-sm text-gray-600">Nessun commento ancora.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($comments as $c)
                            <div class="border rounded p-3">
                                <div class="text-xs text-gray-500 flex items-center justify-between">
                                    <span>
                                        {{ $c->is_admin ? 'Admin' : 'Tu' }}
                                        @if ($c->user) — {{ $c->user->name }} @endif
                                        <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-semibold {{ $c->is_admin ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-900' }}">
                                            {{ $c->is_admin ? 'ADMIN' : 'TU' }}
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

            {{-- Timeline FE (UX pulita) --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Timeline</h3>
                    <span class="text-xs text-gray-500">Storico attività</span>
                </div>

                @if ($feUpdates->count() === 0)
                    <div class="text-sm text-gray-600">Nessun evento.</div>
                @else
                    <ul class="space-y-2 text-sm">
                        @foreach ($feUpdates as $u)
                            @php
                                $meta = $u->meta ?? [];
                                [$badgeText, $badgeClass] = $whoBadge($meta);
                                $byName = data_get($meta, 'admin_name') ?? data_get($meta, 'client_name');
                                $line = $renderUpdate($u);
                            @endphp

                            <li class="flex items-start justify-between gap-4 border rounded p-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </span>

                                        @if ($byName)
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