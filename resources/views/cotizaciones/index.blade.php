<x-app-layout>
    <x-slot name="title">Cotizaciones</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Cotizaciones</h1>
            <p class="text-sm text-gray-500 mt-0.5">Historial de propuestas comerciales</p>
        </div>
    </div>

    @livewire('cotizaciones.lista-global-cotizaciones')
</x-app-layout>
