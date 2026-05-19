<x-app-layout>
    <x-slot name="title">Usuarios</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Usuarios</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestiona los usuarios y sus roles en el sistema</p>
        </div>
    </div>

    @livewire('usuarios.gestion-usuarios')
</x-app-layout>
