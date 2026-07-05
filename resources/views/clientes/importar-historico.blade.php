<x-app-layout>
    <x-slot name="title">Importación histórica</x-slot>

    <div class="mb-4 flex items-center gap-4">
        <a href="{{ route('clientes.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a clientes</a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('clientes.importar') }}" class="text-sm text-gray-500 hover:text-indigo-600">Importador estándar</a>
    </div>

    @livewire('clientes.importar-historico')
</x-app-layout>
