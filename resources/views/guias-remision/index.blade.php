<x-app-layout>
    <x-slot name="title">Guías de Remisión</x-slot>

    <div class="mb-4">
        <h1 class="text-xl font-bold text-gray-800">Guías de Remisión</h1>
        <p class="text-sm text-gray-500 mt-0.5">Listado de todas las guías de remisión registradas.</p>
    </div>

    @livewire('guias-remision.lista-guias-remision')
</x-app-layout>
