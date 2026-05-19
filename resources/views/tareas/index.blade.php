<x-app-layout>
    <x-slot name="title">Tareas</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Tareas comerciales</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestiona y controla las tareas del equipo</p>
        </div>
    </div>

    @livewire('tareas.lista-tareas')
</x-app-layout>
