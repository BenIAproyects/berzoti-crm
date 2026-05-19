<x-app-layout>
    <x-slot name="title">Editar campaña</x-slot>

    <div class="mb-4">
        <a href="{{ route('campanas.show', $campana) }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a la campaña</a>
    </div>

    @livewire('campanas.formulario-campana', ['campana' => $campana])
</x-app-layout>
