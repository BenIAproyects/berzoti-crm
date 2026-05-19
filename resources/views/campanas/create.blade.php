<x-app-layout>
    <x-slot name="title">Nueva campaña</x-slot>

    <div class="mb-4">
        <a href="{{ route('campanas.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a campañas</a>
    </div>

    @livewire('campanas.formulario-campana')
</x-app-layout>
