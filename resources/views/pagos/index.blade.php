<x-app-layout>
    <x-slot name="title">Pagos y Cobranzas</x-slot>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Pagos y Cobranzas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Historial de todos los pagos recibidos.</p>
        </div>
    </div>

    @can('pagos.crear')
    @livewire('pagos.formulario-pago')
    @endcan

    @livewire('pagos.lista-pagos')
</x-app-layout>
