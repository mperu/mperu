<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Progetti (Back Office)</h2>

            <a href="{{ route('admin.dashboard') }}">
                <x-secondary-button>
                    ← BO
                </x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-4 bg-green-100 rounded text-green-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                {{-- Filtri --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-700 font-semibold">Filtro status:</span>

                    <a href="{{ route('admin.projects.index') }}">
                        <x-secondary-button>
                            Tutti
                        </x-secondary-button>
                    </a>

                    @foreach (['new','in_progress','review','delivered','closed'] as $s)
                        <a href="{{ route('admin.projects.index', ['status' => $s]) }}">
                            <x-secondary-button>
                                {{ $s }}
                            </x-secondary-button>
                        </a>
                    @endforeach
                </div>

                {{-- Tabella --}}
                <div class="overflow-auto">
                    <table class="table-auto w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">ID</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Cliente</th>
                                <th class="py-2">Ordine</th>
                                <th class="py-2">Subdomain</th>
                                <th class="py-2">Files</th>
                                <th class="py-2">Snapshot</th>
                                <th class="py-2 text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr class="border-b">
                                    <td class="py-2">#{{ $project->id }}</td>

                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-200">
                                            {{ $project->status }}
                                        </span>
                                    </td>

                                    <td class="py-2">
                                        <div class="font-semibold">{{ $project->user->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-500">{{ $project->user->email ?? '' }}</div>
                                    </td>

                                    <td class="py-2">
                                        {{ optional($project->order)->id ? '#'.$project->order->id : '—' }}
                                    </td>

                                    <td class="py-2">
                                        {{ $project->subdomain ?? '—' }}
                                    </td>

                                    <td class="py-2">
                                        {{ (int) $project->files_count }}
                                    </td>

                                    <td class="py-2">
                                        {{ $project->snapshot_path ? '✅' : '—' }}
                                    </td>

                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.projects.show', $project) }}">
                                            <x-primary-button>
                                                Gestisci
                                            </x-primary-button>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-sm text-gray-600">
                                        Nessun progetto trovato.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div>
                    {{ $projects->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>