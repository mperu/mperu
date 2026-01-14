<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Ordini</h2>
    </x-slot>

    <div class="p-6">
        <p class="text-sm text-gray-600 mb-4">
            Qui troverai i tuoi ordini.
        </p>

        <div class="text-sm">
            Totale ordini: <strong>{{ $orders->total() }}</strong>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>