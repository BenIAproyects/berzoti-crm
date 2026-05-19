<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Clientes y prospectos</h2>
            <p class="text-sm text-gray-500">Gestiona toda tu base comercial</p>
        </div>
        <div class="flex items-center gap-2">
            @can('clientes.importar')
            <a href="{{ route('clientes.importar') }}"
               class="px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100">
                Importar Excel
            </a>
            @endcan
            @can('clientes.crear')
            <a href="{{ route('clientes.create') }}"
               class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                + Nuevo cliente
            </a>
            @endcan
        </div>
    </div>

    @livewire('clientes.lista-clientes')
</x-app-layout>
