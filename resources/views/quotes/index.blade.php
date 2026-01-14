<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Preventivi</h2>
            <a href="{{ route('quotes.create') }}" class="underline">+ Nuovo preventivo</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-3 bg-green-100 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if($quotes->count() === 0)
                    <p class="text-sm text-gray-600">
                        Nessun preventivo ancora. Clicca “Nuovo preventivo”.
                    </p>
                @else
                    <table class="table-auto w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">ID</th>
                                <th class="py-2">Stato</th>
                                <th class="py-2">Totale</th>
                                <th class="py-2">Acconto</th>
                                <th class="py-2">Creato</th>
                                <th class="py-2 text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                                <tr class="border-b">
                                    <td class="py-2">{{ $quote->id }}</td>
                                    <td class="py-2">{{ $quote->status }}</td>
                                    <td class="py-2">€ {{ number_format((float)$quote->total_amount, 2, ',', '.') }}</td>
                                    <td class="py-2">€ {{ number_format((float)$quote->deposit_amount, 2, ',', '.') }}</td>
                                    <td class="py-2">{{ $quote->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 text-right">
                                        <a class="underline" href="{{ route('quotes.show', $quote) }}">Dettaglio</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $quotes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>