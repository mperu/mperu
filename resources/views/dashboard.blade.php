<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-2">
                    <div>
                        Ciao <strong>{{ $user->name }}</strong>, sei loggato ✅
                    </div>
                    <div class="text-sm text-gray-600">
                        Tipo account: <strong>{{ $isAdmin ? 'admin' : 'cliente' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Se admin: mostra SOLO area admin --}}
            @if($isAdmin)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                    <div class="p-6">
                        <h3 class="font-semibold mb-3 text-red-600">Area Admin</h3>

                        <ul class="list-disc pl-5 space-y-1">
                            <li>
                                <a class="underline" href="{{ route('admin.dashboard') }}">
                                    Apri Back Office
                                </a>
                            </li>
                            <li>
                                <a class="underline" href="{{ route('admin.users.index') }}">
                                    Gestione utenti
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            {{-- Se cliente: mostra SOLO pannello cliente --}}
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold mb-3">Pannello Cliente FE</h3>

                        <ul class="list-disc pl-5 space-y-1">
                            <li><a href="#" class="underline">I miei progetti</a></li>
                            <li><a href="#" class="underline">Preventivi</a></li>
                            <li><a href="#" class="underline">Ordini</a></li>
                            <li><a href="#" class="underline">Upload materiali</a></li>
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>