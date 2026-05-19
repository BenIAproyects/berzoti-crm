<div>
    {{-- Header con botón nueva tarea --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex flex-wrap gap-3">
            <select wire:model.live="filtroEstado"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="activas">Activas (todas)</option>
                <option value="pendiente">Por iniciar</option>
                <option value="en_proceso">En proceso</option>
                <option value="completada">Completadas</option>
                <option value="">Todas</option>
            </select>
            <select wire:model.live="filtroPrioridad"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas las prioridades</option>
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
            </select>
            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('supervisor'))
            <select wire:model.live="filtroUsuario"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los vendedores</option>
                @foreach($vendedores as $v)
                <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>
            @endif
        </div>

        @role('administrador|supervisor|vendedor')
        <button wire:click="abrirFormulario"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva tarea
        </button>
        @endrole
    </div>

    {{-- Modal nueva tarea --}}
    @if($mostrarFormulario)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">Nueva tarea</h3>
                </div>
                <button wire:click="$set('mostrarFormulario', false)" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="guardar" class="px-6 py-5 space-y-4">

                {{-- Título --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">¿Qué hay que hacer? *</label>
                    <input wire:model="titulo" type="text"
                           placeholder="Ej: Llamar para seguimiento de cotización"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400">
                    @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cliente buscador --}}
                <div x-data="{ abierto: false }"
                     x-on:limpiar-cliente.window="$nextTick(() => { $refs.inputCliente.focus(); abierto = true })"
                     class="relative">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cliente *</label>

                    @if($clienteConfirmado && $cliente_id)
                    {{-- Chip cliente seleccionado --}}
                    <div class="flex items-center justify-between w-full rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-2 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-medium text-indigo-800">{{ $clienteBusqueda }}</span>
                        </div>
                        <button type="button" wire:click="limpiarCliente"
                                class="text-indigo-400 hover:text-red-500 ml-2 transition-colors" title="Cambiar cliente">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @else
                    {{-- Input de búsqueda --}}
                    <input
                        x-ref="inputCliente"
                        wire:model.live.debounce.300ms="clienteBusqueda"
                        x-on:focus="abierto = true"
                        x-on:click.outside="abierto = false"
                        type="text"
                        placeholder="Escribe nombre o RUC para buscar..."
                        autocomplete="off"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400">

                    @if($this->clientesSugeridos->isNotEmpty())
                    <div x-show="abierto"
                         class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        @foreach($this->clientesSugeridos as $c)
                        <button type="button"
                                wire:click="seleccionarCliente({{ $c->id }}, '{{ addslashes($c->razon_social) }}')"
                                x-on:click="abierto = false"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 border-b border-gray-50 last:border-0 flex items-center justify-between">
                            <span class="font-medium text-gray-800">{{ $c->razon_social }}</span>
                            @if($c->ruc)
                            <span class="text-xs text-gray-400 ml-2">{{ $c->ruc }}</span>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    @endif

                    @if(strlen($clienteBusqueda) >= 2 && $this->clientesSugeridos->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">Sin resultados para "{{ $clienteBusqueda }}"</p>
                    @elseif(strlen($clienteBusqueda) < 2)
                    <p class="text-xs text-gray-400 mt-1">Escribe al menos 2 caracteres</p>
                    @endif
                    @endif

                    @error('cliente_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fecha + Prioridad en fila --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Vence el *</label>
                        <input wire:model="fecha_vencimiento" type="date"
                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('fecha_vencimiento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Prioridad</label>
                        <select wire:model="prioridad"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="alta">🔴 Alta</option>
                            <option value="media">🟡 Media</option>
                            <option value="baja">🟢 Baja</option>
                        </select>
                    </div>
                </div>

                {{-- Tipo --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipo de acción</label>
                    <select wire:model="tipo"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin clasificar</option>
                        <option value="llamar">📞 Llamar</option>
                        <option value="visitar">🤝 Visitar</option>
                        <option value="enviar_cotizacion">📄 Enviar cotización</option>
                        <option value="enviar_correo">📧 Enviar correo</option>
                        <option value="seguimiento">🔁 Seguimiento</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nota adicional</label>
                    <textarea wire:model="descripcion" rows="2"
                              placeholder="Contexto o instrucciones..."
                              class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400 resize-none"></textarea>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" wire:click="$set('mostrarFormulario', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Crear tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Lista de tareas --}}
    <div class="space-y-3">
        @forelse($tareas as $tarea)
        @php $vencida = $tarea->estaVencida(); @endphp
        <div wire:key="tarea-{{ $tarea->id }}"
             class="bg-white rounded-xl shadow-sm border {{ $vencida ? 'border-red-200' : 'border-gray-100' }} p-4">
            <div class="flex items-start gap-4">
                {{-- Selector de estado --}}
                <div class="shrink-0 mt-0.5">
                    <select wire:change="cambiarEstado({{ $tarea->id }}, $event.target.value)"
                            class="text-xs rounded-lg border-0 py-1 px-2 font-medium cursor-pointer focus:ring-2 focus:ring-indigo-500
                                {{ \App\Models\Tarea::colorEstado($tarea->estado) }}">
                        <option value="pendiente"  {{ $tarea->estado === 'pendiente'  ? 'selected' : '' }}>Por iniciar</option>
                        <option value="en_proceso" {{ $tarea->estado === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                        <option value="completada" {{ $tarea->estado === 'completada' ? 'selected' : '' }}>Completada</option>
                    </select>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-medium {{ $tarea->estado === 'completada' ? 'line-through text-gray-400' : ($vencida ? 'text-red-700' : 'text-gray-800') }}">
                            {{ $tarea->titulo }}
                        </p>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($vencida)
                            <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium">Vencida</span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ match($tarea->prioridad) {
                                    'alta'  => 'bg-red-50 text-red-600',
                                    'baja'  => 'bg-gray-100 text-gray-500',
                                    default => 'bg-yellow-50 text-yellow-600',
                                } }}">
                                {{ ucfirst($tarea->prioridad) }}
                            </span>
                            <button
                                x-data
                                x-on:click="if(confirm('¿Eliminar esta tarea?')) $wire.eliminar({{ $tarea->id }})"
                                class="text-gray-300 hover:text-red-500 transition-colors"
                                title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <a href="{{ route('clientes.show', $tarea->cliente_id) }}"
                           class="text-xs text-indigo-600 hover:underline">
                            {{ $tarea->cliente->razon_social ?? '—' }}
                        </a>
                        <span class="text-xs text-gray-400">
                            {{ $tarea->fecha_vencimiento->format('d/m/Y') }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $tarea->usuario->name }}</span>
                        @if($tarea->tipo)
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                            {{ $tarea->tipo->label() }}
                        </span>
                        @endif
                    </div>

                    @if($tarea->descripcion)
                    <p class="text-xs text-gray-500 mt-1">{{ $tarea->descripcion }}</p>
                    @endif

                    @if($tarea->estado === 'completada' && $tarea->completada_en)
                    <p class="text-xs text-green-600 mt-1">✓ Completada el {{ $tarea->completada_en->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <p class="text-sm">No hay tareas en este filtro.</p>
        </div>
        @endforelse
    </div>

    @if($tareas->hasPages())
    <div class="mt-4">{{ $tareas->links() }}</div>
    @endif
</div>
