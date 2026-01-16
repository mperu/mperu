{{-- resources/views/quotes/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h5 mb-0">Preventivo #{{ $quote->id }}</h2>
                <small class="text-muted">Dettaglio preventivo</small>
            </div>

            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Preventivi
            </a>
        </div>
    </x-slot>

    @php
        $status = $quote->status;

        $badgeClass = match ($status) {
            'draft' => 'text-bg-warning',
            'accepted' => 'text-bg-success',
            'rejected' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };

        $gross = (float) ($quote->total_amount ?? 0);
        $withholdingRate = (int) ($quote->withholding_rate ?? 20);
        $withholding = (float) ($quote->withholding_amount ?? 0);
        $stampDuty = (float) ($quote->stamp_duty_amount ?? 0);
        $totalToPay = (float) ($quote->net_amount ?? 0);

        $deposit = (float) ($quote->deposit_amount ?? 0);
        $balance = (float) ($quote->balance_amount ?? 0);

        $eur = fn($n) => '€ ' . number_format((float)$n, 2, ',', '.');
    @endphp

    <div class="py-4">
        <div class="container">

            @if (session('status'))
                <div class="alert alert-success mb-3">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Stato</span>
                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                    </div>

                    @if (optional($quote->order)->id)
                        <a href="{{ route('orders.show', $quote->order) }}" class="btn btn-outline-secondary btn-sm">
                            Vai all’ordine #{{ $quote->order->id }}
                        </a>
                    @endif
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-9">

                            {{-- RIEPILOGO --}}
                            <div class="mb-4">
                                <div class="text-uppercase text-muted small fw-semibold mb-2">Riepilogo</div>

                                <div class="border rounded overflow-hidden">
                                    <table class="table table-sm mb-0 align-middle">
                                        <colgroup>
                                            <col>
                                            <col style="width: 220px;">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td class="py-2 px-3">Compenso (il tuo prezzo)</td>
                                                <td class="py-2 px-3 text-end fw-semibold">{{ $eur($gross) }}</td>
                                            </tr>

                                            <tr>
                                                <td class="py-2 px-3">
                                                    Ritenuta d’acconto {{ $withholdingRate }}% (da versare)
                                                </td>
                                                <td class="py-2 px-3 text-end">+ {{ $eur($withholding) }}</td>
                                            </tr>

                                            <tr>
                                                <td class="py-2 px-3">Marca da bollo</td>
                                                <td class="py-2 px-3 text-end">+ {{ $eur($stampDuty) }}</td>
                                            </tr>

                                            <tr class="table-light">
                                                <td class="py-3 px-3 fw-bold border-top">Totale da pagare</td>
                                                <td class="py-3 px-3 text-end fw-bold border-top">
                                                    <span class="fs-5">{{ $eur($totalToPay) }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- PAGAMENTI --}}
                            <div class="mb-4">
                                <div class="text-uppercase text-muted small fw-semibold mb-2">Pagamenti</div>

                                <div class="border rounded overflow-hidden">
                                    <table class="table table-sm mb-0 align-middle">
                                        <colgroup>
                                            <col>
                                            <col style="width: 220px;">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td class="py-2 px-3">Acconto (su totale da pagare)</td>
                                                <td class="py-2 px-3 text-end fw-semibold">{{ $eur($deposit) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 px-3">Saldo (su totale da pagare)</td>
                                                <td class="py-2 px-3 text-end fw-semibold">{{ $eur($balance) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- AZIONI --}}
                            <div class="d-flex justify-content-end gap-2">
                                @if ($quote->status === 'draft')
                                    <form method="POST"
                                          action="{{ route('quotes.destroy', $quote) }}"
                                          onsubmit="return confirm('Confermi di NON accettare questo preventivo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            Non accetto
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('quotes.accept', $quote) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            Accetta preventivo
                                        </button>
                                    </form>
                                @else
                                    <div class="text-muted small">
                                        Questo preventivo non è modificabile perché è: <strong>{{ $quote->status }}</strong>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>