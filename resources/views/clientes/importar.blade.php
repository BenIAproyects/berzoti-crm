<x-app-layout>
    <x-slot name="title">Importar clientes</x-slot>

    <div class="mb-4 flex items-center gap-4">
        <a href="{{ route('clientes.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a clientes</a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('clientes.importar-historico') }}"
           class="text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-lg hover:bg-amber-100">
            Importar base histórica (Excel normalizado)
        </a>
    </div>

    @livewire('clientes.importar-clientes')
</x-app-layout>
