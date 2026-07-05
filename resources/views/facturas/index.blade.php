<x-app-layout>
    <x-slot name="title">Facturas</x-slot>

    <div class="mb-4">
        <h1 class="text-xl font-bold text-gray-800">Facturas</h1>
        <p class="text-sm text-gray-500 mt-0.5">Listado de todas las facturas registradas.</p>
    </div>

    @can('pagos.crear')
    @livewire('pagos.formulario-pago')
    @endcan

    @livewire('facturas.lista-facturas')
</x-app-layout>
