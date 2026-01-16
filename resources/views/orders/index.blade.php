<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Ordini</h2>

            <a href="{{ route('dashboard') }}" class="underline text-gray-800 hover:text-gray-600">
                ← Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">
                    Qui troverai i tuoi ordini.
                </p>

                <div class="mt-2 text-sm">
                    Totale ordini: <strong>{{ $orders->total() }}</strong>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if($orders->count() === 0)
                    <p class="text-sm text-gray-600">
                        Nessun ordine presente.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">ID</th>
                                    <th class="py-2 pr-4">Stato</th>
                                    <th class="py-2 pr-4">Totale</th>
                                    <th class="py-2 pr-4">Acconto</th>
                                    <th class="py-2 pr-4">Creato</th>
                                    <th class="py-2 text-right"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    @php
                                        $status = $order->status;

                                        $badgeClass = match ($status) {
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'deposit_paid' => 'bg-blue-100 text-blue-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp

                                    <tr class="border-b last:border-b-0">
                                        <td class="py-3 pr-4">#{{ $order->id }}</td>

                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
                                                {{ $status }}
                                            </span>
                                        </td>

                                        <td class="py-3 pr-4">
                                            € {{ number_format((float)$order->total_amount, 2, ',', '.') }}
                                        </td>

                                        <td class="py-3 pr-4">
                                            € {{ number_format((float)$order->deposit_amount, 2, ',', '.') }}
                                        </td>

                                        <td class="py-3 pr-4 text-gray-600">
                                            {{ $order->created_at?->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="py-3 text-right">
                                            <a class="underline text-gray-800 hover:text-gray-600"
                                               href="{{ route('orders.show', $order) }}">
                                                Dettaglio
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>