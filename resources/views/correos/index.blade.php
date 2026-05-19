<x-app-layout>
    <x-slot name="title">Correos enviados</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Historial de correos enviados</h2>
        <p class="text-sm text-gray-500">Registro completo de todos los envíos del sistema</p>
    </div>

    @livewire('correos.historial-correos')
</x-app-layout>
