<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Back Office</h2>
    </x-slot>

    <div class="p-6 space-y-4">

        <div class="bg-white shadow-sm rounded-lg p-6">
            <p class="mb-4">BO pronto ✅</p>

            <ul class="list-disc pl-5 space-y-1">
                <li>
                    <a href="{{ route('admin.users.index') }}" class="underline text-gray-800 hover:text-gray-600">
                        Gestione utenti
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.projects.index') }}" class="underline text-gray-800 hover:text-gray-600">
                        Progetti
                    </a>
                </li>

                <li><span class="text-gray-400">Preventivi (next)</span></li>
                <li><span class="text-gray-400">Ordini (next)</span></li>
            </ul>
        </div>

    </div>
</x-app-layout>