<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Crea preventivo</h2>
            <a href="{{ route('quotes.index') }}" class="underline">← Preventivi</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                @if ($errors->any())
                    <div class="p-3 bg-red-100 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('quotes.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-1">Titolo (opzionale)</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full rounded border-gray-300"
                            placeholder="Es. Sito vetrina 5 pagine"
                        >
                    </div>

                    <div class="text-sm text-gray-600">
                        MVP: per ora il preventivo ha importi dummy e config_json di esempio.
                    </div>

                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">
                        Crea preventivo
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>