{{-- resources/views/projects/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">Progetti</h2>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                ← Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">

            @if (session('status'))
                <div class="alert alert-success mb-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">

                    <p class="text-muted mb-3">
                        Qui trovi i tuoi progetti. Un progetto nasce quando un ordine diventa <strong>paid</strong>.
                    </p>

                    @if($projects->count() === 0)
                        <div class="text-muted">
                            Nessun progetto ancora.
                            Vai su
                            <a href="{{ route('orders.index') }}" class="text-decoration-underline">
                                Ordini
                            </a>.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Ordine</th>
                                        <th>Subdomain</th>
                                        <th>File</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                        <tr>
                                            <td>#{{ $project->id }}</td>
                                            <td>{{ $project->status }}</td>
                                            <td>
                                                {{ optional($project->order)->id ? '#'.$project->order->id : '—' }}
                                            </td>
                                            <td>{{ $project->subdomain ?? '—' }}</td>
                                            <td>{{ $project->files?->count() ?? 0 }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('projects.show', $project) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    Apri
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $projects->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>