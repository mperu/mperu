<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Gestione utenti</h2>
            <a href="{{ route('admin.dashboard') }}" class="underline">← Back Office</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-3 bg-green-100 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-100 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <table class="table-auto w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">ID</th>
                                <th class="py-2">Nome</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Tipo</th>
                                <th class="py-2 text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b">
                                    <td class="py-2">{{ $user->id }}</td>
                                    <td class="py-2">{{ $user->name }}</td>
                                    <td class="py-2">{{ $user->email }}</td>
                                    <td class="py-2">
                                        @if($user->is_admin)
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">admin</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">cliente</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-right">
                                        <form method="POST" action="{{ route('admin.users.toggleAdmin', $user) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button class="underline" type="submit">
                                                {{ $user->is_admin ? 'Rimuovi admin' : 'Rendi admin' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>