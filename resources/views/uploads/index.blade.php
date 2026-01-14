<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Upload materiali</h2>
    </x-slot>

    <div class="p-6">
        <p class="text-sm text-gray-600 mb-4">
            Qui potrai caricare e gestire i file dei tuoi progetti.
        </p>

        <div class="text-sm">
            Totale file: <strong>{{ $files->total() }}</strong>
        </div>

        <div class="mt-4">
            {{ $files->links() }}
        </div>
    </div>
</x-app-layout>