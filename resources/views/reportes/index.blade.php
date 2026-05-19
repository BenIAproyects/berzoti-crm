<x-app-layout>
    <x-slot name="title">Reportes</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Reportes</h1>
            <p class="text-sm text-gray-500 mt-0.5">Analiza la actividad comercial y exporta datos</p>
        </div>
    </div>

    @livewire('reportes.reporte-general')
</x-app-layout>
