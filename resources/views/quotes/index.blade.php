{{-- resources/views/quotes/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">Preventivi</h2>

            <a href="{{ route('templates.index') }}" class="btn btn-primary btn-sm">
                + Nuovo preventivo
            </a>
        </div>
    </x-slot>

    @php
        $eur = fn($n) => '€ ' . number_format((float)$n, 2, ',', '.');

        $badgeClass = fn($status) => match ($status) {
            'draft' => 'text-bg-warning',
            'accepted' => 'text-bg-success',
            'rejected' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    @endphp

    <div class="py-4">
        <div class="container">

            @if (session('status'))
                <div class="alert alert-success mb-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    @if($quotes->count() === 0)
                        <p class="text-muted mb-0">
                            Nessun preventivo ancora. Clicca “Nuovo preventivo”.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Stato</th>
                                        <th scope="col">Totale</th>
                                        <th scope="col">Acconto</th>
                                        <th scope="col">Creato</th>
                                        <th scope="col" class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotes as $quote)
                                        <tr>
                                            <td class="fw-medium">#{{ $quote->id }}</td>
                                            <td>
                                                <span class="badge {{ $badgeClass($quote->status) }}">
                                                    {{ $quote->status }}
                                                </span>
                                            </td>
                                            <td>{{ $eur($quote->net_amount ?? 0) }}</td>
                                            <td>{{ $eur($quote->deposit_amount ?? 0) }}</td>
                                            <td class="text-muted">
                                                {{ optional($quote->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary btn-sm">
                                                    Dettaglio
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $quotes->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>