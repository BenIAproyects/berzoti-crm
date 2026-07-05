<div class="space-y-4">

    {{-- Flash --}}
    @if(session('success_contactos'))
    <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2">
        {{ session('success_contactos') }}
    </div>
    @endif

    {{-- Cabecera --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $contactos->count() }} contacto(s) registrado(s)</p>
        @unless($mostrarFormulario)
        <button wire:click="nuevo"
                class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Agregar contacto
        </button>
        @endunless
    </div>

    {{-- Formulario inline --}}
    @if($mostrarFormulario)
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
        <h4 class="text-sm font-semibold text-indigo-700 mb-4">
            {{ $editId ? 'Editar contacto' : 'Nuevo contacto' }}
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input wire:model="nombre_contacto" type="text"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('nombre_contacto') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Cargo</label>
                <input wire:model="cargo" type="text"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Teléfono</label>
                <input wire:model="telefono" type="text"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Correo</label>
                <input wire:model="correo" type="email"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('correo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea wire:model="observaciones" rows="2"
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <input wire:model="es_principal" type="checkbox" id="es_principal_c"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="es_principal_c" class="text-sm text-gray-700">Marcar como contacto principal</label>
            </div>

        </div>
        <div class="flex items-center gap-3 mt-4">
            <button wire:click="guardar"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                <span wire:loading.remove wire:target="guardar">{{ $editId ? 'Guardar cambios' : 'Agregar' }}</span>
                <span wire:loading wire:target="guardar">Guardando...</span>
            </button>
            <button wire:click="cancelar"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancelar
            </button>
        </div>
    </div>
    @endif

    {{-- Lista --}}
    @forelse($contactos as $contacto)
    <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-start justify-between gap-4 hover:border-gray-200 transition-colors">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-medium text-gray-800 text-sm">{{ $contacto->nombre_contacto }}</span>
                @if($contacto->es_principal)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Principal</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                @if($contacto->cargo)
                    <span>{{ $contacto->cargo }}</span>
                @endif
                @if($contacto->telefono)
                    <span>📞 {{ $contacto->telefono }}</span>
                @endif
                @if($contacto->correo)
                    <span>✉ {{ $contacto->correo }}</span>
                @endif
            </div>
            @if($contacto->observaciones)
                <p class="mt-1 text-xs text-gray-400 italic">{{ $contacto->observaciones }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            @unless($contacto->es_principal)
            <button wire:click="togglePrincipal({{ $contacto->id }})"
                    class="text-xs text-gray-400 hover:text-indigo-600 underline" title="Marcar como principal">
                Principal
            </button>
            @endunless
            <button wire:click="editar({{ $contacto->id }})"
                    class="text-gray-400 hover:text-amber-600" title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
            <button wire:click="eliminar({{ $contacto->id }})"
                    wire:confirm="¿Eliminar este contacto?"
                    class="text-gray-400 hover:text-red-600" title="Eliminar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </div>
    @empty
    <div class="text-center py-10 text-gray-400 text-sm">
        No hay contactos registrados para este cliente.
    </div>
    @endforelse

</div>
