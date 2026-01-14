<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">
                Preventivo #{{ $quote->id }}
            </h2>

            <a href="{{ route('quotes.index') }}" class="underline text-gray-800 hover:text-gray-600">
                ← Preventivi
            </a>
        </div>
    </x-slot>

    @php
        $status = $quote->status;

        $badgeClass = match ($status) {
            'draft' => 'bg-yellow-100 text-yellow-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-3 bg-green-100 rounded text-green-900">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-100 rounded text-red-900">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <strong>Stato:</strong>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </div>

                    {{-- Se esiste ordine collegato (quote->order) --}}
                    @if (optional($quote->order)->id)
                        <a href="{{ route('orders.show', $quote->order) }}" class="underline text-gray-800 hover:text-gray-600 text-sm">
                            Vai all’ordine #{{ $quote->order->id }}
                        </a>
                    @endif
                </div>

                <div class="space-y-1 text-sm">
                    <div><strong>Totale:</strong> € {{ number_format((float) $quote->total_amount, 2, ',', '.') }}</div>
                    <div><strong>Acconto:</strong> € {{ number_format((float) $quote->deposit_amount, 2, ',', '.') }}</div>
                    <div><strong>Saldo:</strong> € {{ number_format((float) $quote->balance_amount, 2, ',', '.') }}</div>
                </div>

                <hr>

                <div class="text-sm text-gray-700">
                    <strong>Config JSON (MVP):</strong>
                    <pre class="mt-2 p-3 bg-gray-50 rounded text-xs overflow-auto">{{ json_encode($quote->config_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <hr>

                @if ($quote->status === 'draft')
                    <form method="POST" action="{{ route('quotes.accept', $quote) }}" class="pt-1">
                        @csrf

                        <x-primary-button>
                            Accetta preventivo
                        </x-primary-button>
                    </form>
                @else
                    <p class="text-sm text-gray-600">
                        Questo preventivo non è più accettabile perché è:
                        <strong>{{ $quote->status }}</strong>
                    </p>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>