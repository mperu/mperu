<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Ordine #{{ $order->id }}</h2>
            <a href="{{ route('orders.index') }}" class="underline text-gray-800 hover:text-gray-600">← Ordini</a>
        </div>
    </x-slot>

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

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <div class="text-sm"><strong>Status:</strong> {{ $order->status }}</div>
                <div class="text-sm"><strong>Totale:</strong> € {{ number_format((float)$order->total_amount, 2, ',', '.') }}</div>
                <div class="text-sm"><strong>Acconto:</strong> € {{ number_format((float)$order->deposit_amount, 2, ',', '.') }}</div>
                <div class="text-sm"><strong>Saldo:</strong> € {{ number_format((float)$order->balance_amount, 2, ',', '.') }}</div>

                <div class="text-sm text-gray-600">
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

                @if(optional($order->project)->id)
                    <div class="text-sm text-gray-600">
                        Progetto creato:
                        <a class="underline text-gray-800 hover:text-gray-600" href="{{ route('projects.index') }}">
                            Progetto #{{ $order->project->id }} ({{ $order->project->status }})
                        </a>
                    </div>
                @endif

                <hr>

                {{-- Azioni MVP (simulazione pagamenti) --}}
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
                            Ordine completato
                        </span>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>