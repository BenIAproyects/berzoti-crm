<x-app-layout>
    <x-slot name="title">Órdenes de Compra</x-slot>

    <div class="mb-4">
        <h1 class="text-xl font-bold text-gray-800">Órdenes de Compra</h1>
        <p class="text-sm text-gray-500 mt-0.5">Listado de todas las órdenes de compra registradas.</p>
    </div>

    @livewire('ordenes-compra.lista-ordenes-compra')
</x-app-layout>
