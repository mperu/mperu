{{-- resources/views/templates/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h5 mb-0">Scegli un template</h2>
                <small class="text-muted">Seleziona un pacchetto di partenza</small>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $fmt = fn(int $cents) => '€ ' . number_format($cents / 100, 2, ',', '.');
    @endphp

    <div class="py-4">
        <div class="container">

            {{-- INFO --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Seleziona un pacchetto (Bronze / Silver / Gold).
                        Poi potrai configurare le opzioni e generare il preventivo.
                    </p>
                </div>
            </div>

            {{-- GRID --}}
            <div class="row g-4">
                @foreach($templates as $t)
                    <div class="col-12 col-md-4">
                        <div class="card h-100 shadow-sm border-0">

                            <div class="card-body d-flex flex-column">

                                {{-- HEADER --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 fw-semibold">
                                        {{ $t->name }}
                                    </h5>

                                    <span class="badge rounded-pill bg-light text-dark border fw-normal">
                                        {{ $fmt((int) $t->base_price) }}
                                    </span>
                                </div>

                                {{-- DESCRIPTION --}}
                                @if($t->description)
                                    <p class="card-text text-muted small mt-2">
                                        {!! nl2br(e($t->description)) !!}
                                    </p>
                                @endif

                                {{-- FOOTER --}}
                                <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        slug: {{ $t->slug }}
                                    </span>

                                    <a href="{{ route('quotes.create', ['template' => $t->slug]) }}"
                                       class="btn btn-primary btn-sm px-3">
                                        Scegli
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>