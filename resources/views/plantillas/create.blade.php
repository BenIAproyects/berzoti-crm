<x-app-layout>
    <x-slot name="title">Nueva plantilla</x-slot>
    <div class="mb-4">
        <a href="{{ route('plantillas.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a plantillas</a>
    </div>
    @livewire('plantillas.formulario-plantilla')
</x-app-layout>
