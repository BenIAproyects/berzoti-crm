<x-app-layout>
    <x-slot name="title">Editar cliente</x-slot>

    <div class="mb-4">
        <a href="{{ route('clientes.show', $cliente) }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a la ficha</a>
    </div>

    @livewire('clientes.formulario-cliente', ['cliente' => $cliente])
</x-app-layout>
