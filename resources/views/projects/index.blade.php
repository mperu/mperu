<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">I miei progetti</h2>
    </x-slot>

    <div class="p-6">
        <p class="text-sm text-gray-600 mb-4">
            Qui troverai i progetti attivi e conclusi.
        </p>

        <div class="text-sm">
            Totale progetti: <strong>{{ $projects->total() }}</strong>
        </div>

        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</x-app-layout>