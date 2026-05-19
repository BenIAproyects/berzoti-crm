<x-app-layout>
    <x-slot name="title">Plantillas de correo</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Plantillas de correo</h2>
            <p class="text-sm text-gray-500">Mensajes reutilizables con variables dinámicas</p>
        </div>
        @can('plantillas.crear')
        <a href="{{ route('plantillas.create') }}"
           class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            + Nueva plantilla
        </a>
        @endcan
    </div>

    @livewire('plantillas.lista-plantillas')
</x-app-layout>
