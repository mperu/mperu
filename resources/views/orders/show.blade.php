<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Ordine #{{ $order->id }}</h2>
            <a href="{{ route('orders.index') }}" class="underline text-gray-800 hover:text-gray-600">← Ordini</a>
        </div>
    </x-slot>

    @php
        $status = $order->status;

        $badgeClass = match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'deposit_paid' => 'bg-indigo-100 text-indigo-800',
            'paid' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-200 text-gray-800',
            'refunded' => 'bg-red-100 text-red-800',
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
                        <strong>Status:</strong>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </div>

                    {{-- Shortcut al progetto se esiste --}}
                    @if(optional($order->project)->id)
                        <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('projects.show', $order->project) }}">
                            Apri progetto #{{ $order->project->id }}
                        </a>
                    @endif
                </div>

                <div class="space-y-1 text-sm">
                    <div><strong>Totale:</strong> € {{ number_format((float)$order->total_amount, 2, ',', '.') }}</div>
                    <div><strong>Acconto:</strong> € {{ number_format((float)$order->deposit_amount, 2, ',', '.') }}</div>
                    <div><strong>Saldo:</strong> € {{ number_format((float)$order->balance_amount, 2, ',', '.') }}</div>
                </div>

                <div class="text-sm text-gray-600 space-y-1">
                    <div><strong>Acconto pagato il:</strong> {{ $order->deposit_paid_at ? $order->deposit_paid_at->format('d/m/Y H:i') : '—' }}</div>
                    <div><strong>Saldo pagato il:</strong> {{ $order->balance_paid_at ? $order->balance_paid_at->format('d/m/Y H:i') : '—' }}</div>
                </div>

                <hr>

                <div class="text-sm text-gray-600">
                    Preventivo collegato:
                    @if($order->quote_id)
                        <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('quotes.show', $order->quote_id) }}">
                            Preventivo #{{ $order->quote_id }}
                        </a>
                    @else
                        —
                    @endif
                </div>

                {{-- Blocco progetto --}}
                @if(optional($order->project)->id)
                    <div class="text-sm text-gray-600">
                        Progetto creato:
                        <span class="font-semibold">#{{ $order->project->id }}</span>
                        <span class="text-gray-500">({{ $order->project->status }})</span>
                        —
                        <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('projects.show', $order->project) }}">
                            Apri progetto
                        </a>
                    </div>
                @elseif($order->status === 'paid')
                    <div class="text-sm text-gray-600">
                        Ordine completato: il progetto dovrebbe essere disponibile.
                        <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('projects.index') }}">
                            Vai ai progetti
                        </a>
                    </div>
                @endif

                <hr>

                {{-- Azioni MVP: simulazione pagamenti --}}
                <div class="flex items-center gap-3">

                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('orders.depositPaid', $order) }}">
                            @csrf
                            @method('PATCH')
                            <x-primary-button>
                                Segna acconto pagato
                            </x-primary-button>
                        </form>
                    @endif

                    @if($order->status === 'deposit_paid')
                        <form method="POST" action="{{ route('orders.balancePaid', $order) }}">
                            @csrf
                            @method('PATCH')
                            <x-primary-button>
                                Segna saldo pagato
                            </x-primary-button>
                        </form>
                    @endif

                    @if($order->status === 'paid')
                        <span class="text-sm text-green-700 font-semibold">
                            Ordine completato ✅
                        </span>
                    @endif

                </div>

            </div>

        </div>
    </div>
</x-app-layout>