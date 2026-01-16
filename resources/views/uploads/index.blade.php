{{-- resources/views/uploads/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h5 mb-0">Upload materiali</h2>
                <div class="text-muted small">Gestione file progetto</div>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">

            {{-- INFO --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="text-muted mb-2">
                        Qui puoi caricare e gestire i file dei tuoi progetti.
                    </p>

                    <div class="small">
                        Totale file:
                        <strong>{{ $files->total() }}</strong>
                    </div>
                </div>
            </div>

            {{-- LISTA FILE (placeholder finché non aggiungi tabella) --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    @if ($files->count() === 0)
                        <div class="text-muted text-center py-4">
                            Nessun file caricato.
                        </div>
                    @else
                        {{-- Qui in futuro metterai la tabella --}}
                        <div class="text-muted small mb-3">
                            Elenco file (work in progress)
                        </div>
                    @endif

                </div>

                {{-- PAGINAZIONE --}}
                @if ($files->hasPages())
                    <div class="card-footer bg-white">
                        {{ $files->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>