<div class="space-y-4">

    {{-- Flash --}}
    @if(session('success_correos'))
    <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2">
        {{ session('success_correos') }}
    </div>
    @endif

    {{-- Cabecera --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $correos->count() }} correo(s) registrado(s)</p>
        @unless($mostrarFormulario)
        <button wire:click="nuevo"
                class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Agregar correo
        </button>
        @endunless
    </div>

    {{-- Formulario inline --}}
    @if($mostrarFormulario)
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
        <h4 class="text-sm font-semibold text-indigo-700 mb-4">
            {{ $editId ? 'Editar correo' : 'Nuevo correo' }}
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input wire:model="email" type="email"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nombre del contacto</label>
                <input wire:model="nombre" type="text" placeholder="Opcional"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Fuente</label>
                <input wire:model="fuente" type="text" placeholder="BASE, WEB, REFERIDO..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model="estado"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($estados as $e)
                        <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <div class="flex items-center gap-2">
                    <input wire:model="es_principal" type="checkbox" id="es_principal_e"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="es_principal_e" class="text-sm text-gray-700">Correo principal</label>
                </div>
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
    @forelse($correos as $correo)
    <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center justify-between gap-4 hover:border-gray-200 transition-colors">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-medium text-gray-800 text-sm truncate">{{ $correo->email }}</span>
                @if($correo->es_principal)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Principal</span>
                @endif
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $correo->estado->value === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $correo->estado->label() }}
                </span>
                @if($correo->contacto_id)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">Contacto</span>
                @endif
            </div>
            @if($correo->nombre)
                <p class="text-xs text-gray-600 font-medium">{{ $correo->nombre }}</p>
            @endif
            @if($correo->fuente)
                <p class="text-xs text-gray-400">Fuente: {{ $correo->fuente }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button wire:click="toggleEstado({{ $correo->id }})"
                    class="text-xs text-gray-400 hover:text-indigo-600 underline" title="Cambiar estado">
                {{ $correo->estado->value === 'activo' ? 'Desactivar' : 'Activar' }}
            </button>
            <button wire:click="editar({{ $correo->id }})"
                    class="text-gray-400 hover:text-amber-600" title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
            <button wire:click="eliminar({{ $correo->id }})"
                    wire:confirm="¿Eliminar este correo?"
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
        No hay correos registrados para este cliente.
    </div>
    @endforelse

</div>
