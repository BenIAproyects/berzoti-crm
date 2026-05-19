<x-app-layout>
    <x-slot name="title">Importar clientes</x-slot>

    <div class="mb-4">
        <a href="{{ route('clientes.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a clientes</a>
    </div>

    @livewire('clientes.importar-clientes')
</x-app-layout>
