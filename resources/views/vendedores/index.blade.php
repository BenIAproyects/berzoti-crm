<x-app-layout>
    <x-slot name="title">Vendedores</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Gestión de Vendedores</h2>
        <p class="text-sm text-gray-500">Asigna clientes a cada vendedor de tu equipo</p>
    </div>

    @livewire('vendedores.gestion-vendedores')
</x-app-layout>
