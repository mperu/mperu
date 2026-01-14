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
                        Tipo account:
                        <strong>{{ $isAdmin ? 'admin' : 'cliente' }}</strong>
                    </div>
                </div>
            </div>

            {{-- ADMIN DASHBOARD (solo admin) --}}
            @if($isAdmin)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                    <div class="p-6 space-y-3">
                        <h3 class="font-semibold text-red-600">Area Admin</h3>

                        <ul class="list-disc pl-5 space-y-1 text-sm">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="underline">
                                    Back Office
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="underline">
                                    Gestione utenti
                                </a>
                            </li>
                        </ul>

                        <p class="text-xs text-gray-500">
                            Le funzionalità BO sono accessibili solo da questa area.
                        </p>
                    </div>
                </div>

            {{-- CLIENT DASHBOARD (solo clienti) --}}
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <h3 class="font-semibold">Pannello Cliente</h3>

                        <ul class="list-disc pl-5 space-y-1 text-sm">
                            <li>
                                <a href="{{ route('projects.index') }}" class="underline">
                                    I miei progetti
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('quotes.index') }}" class="underline">
                                    Preventivi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('orders.index') }}" class="underline">
                                    Ordini
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('uploads.index') }}" class="underline">
                                    Upload materiali
                                </a>
                            </li>
                        </ul>

                        <p class="text-xs text-gray-500">
                            Da qui puoi gestire preventivi, ordini e materiali dei tuoi progetti.
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>