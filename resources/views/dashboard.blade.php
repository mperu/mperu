{{-- resources/views/dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h5 mb-0">Dashboard</h2>
                <div class="text-muted small">Panoramica</div>
            </div>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $quotesCount = method_exists($user, 'quotes') ? $user->quotes()->count() : 0;
        $ordersCount = method_exists($user, 'orders') ? $user->orders()->count() : 0;
        $projectsCount = method_exists($user, 'projects') ? $user->projects()->count() : 0;
    @endphp

    <div class="py-4">
        <div class="container">
            <div class="row g-4">

                {{-- Welcome --}}
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="mb-1">
                                Ciao <strong>{{ $user->name }}</strong>, sei loggato ✅
                            </div>
                            <div class="text-muted small">
                                Tipo account: <strong>{{ $isAdmin ? 'admin' : 'cliente' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($isAdmin)
                    {{-- Admin --}}
                    <div class="col-12">
                        <div class="card shadow-sm border-danger-subtle">
                            <div class="card-body">
                                <h3 class="h6 text-danger mb-3">Area Admin</h3>

                                <div class="list-group">
                                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                       href="{{ route('admin.dashboard') }}">
                                        <span>Apri Back Office</span>
                                        <span class="text-muted">→</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                       href="{{ route('admin.users.index') }}">
                                        <span>Gestione utenti</span>
                                        <span class="text-muted">→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Cliente --}}
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="mb-3">
                                    <h3 class="h6 mb-1">Pannello Cliente</h3>
                                    <p class="text-muted small mb-0">
                                        Gestisci preventivi, ordini e progetti. I materiali si caricano dentro al progetto.
                                    </p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="border rounded-3 p-3 bg-light h-100">
                                            <div class="text-muted small">Preventivi</div>
                                            <div class="display-6 fw-semibold lh-1 mb-2">{{ $quotesCount }}</div>
                                            <a class="link-primary small" href="{{ route('quotes.index') }}">Apri</a>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="border rounded-3 p-3 bg-light h-100">
                                            <div class="text-muted small">Ordini</div>
                                            <div class="display-6 fw-semibold lh-1 mb-2">{{ $ordersCount }}</div>
                                            <a class="link-primary small" href="{{ route('orders.index') }}">Apri</a>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="border rounded-3 p-3 bg-light h-100">
                                            <div class="text-muted small">Progetti</div>
                                            <div class="display-6 fw-semibold lh-1 mb-2">{{ $projectsCount }}</div>
                                            <a class="link-primary small" href="{{ route('projects.index') }}">Apri</a>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                                        + Nuovo preventivo
                                    </a>

                                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                                        Preventivi
                                    </a>

                                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                        Ordini
                                    </a>

                                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                                        Progetti (upload materiali)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>