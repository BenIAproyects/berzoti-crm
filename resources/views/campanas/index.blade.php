<x-app-layout>
    <x-slot name="title">Campañas</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Campañas comerciales</h2>
            <p class="text-sm text-gray-500">Organiza y gestiona tus campañas de panetones</p>
        </div>
        @role('administrador|supervisor')
        <a href="{{ route('campanas.create') }}"
           class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            + Nueva campaña
        </a>
        @endrole
    </div>

    @livewire('campanas.lista-campanas')
</x-app-layout>
