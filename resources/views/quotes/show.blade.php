<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Preventivo #{{ $quote->id }}</h2>
            <a href="{{ route('quotes.index') }}" class="underline">← Preventivi</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-3 bg-green-100 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <div><strong>Stato:</strong> {{ $quote->status }}</div>
                <div><strong>Totale:</strong> € {{ number_format((float)$quote->total_amount, 2, ',', '.') }}</div>
                <div><strong>Acconto:</strong> € {{ number_format((float)$quote->deposit_amount, 2, ',', '.') }}</div>
                <div><strong>Saldo:</strong> € {{ number_format((float)$quote->balance_amount, 2, ',', '.') }}</div>

                <hr>

                <div class="text-sm text-gray-700">
                    <strong>Config JSON (MVP):</strong>
                    <pre class="mt-2 p-3 bg-gray-50 rounded text-xs overflow-auto">{{ json_encode($quote->config_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>