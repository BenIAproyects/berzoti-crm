<div>
    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input wire:model.live.debounce.300ms="busqueda" type="text"
                       placeholder="Buscar por empresa..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $e)
                    <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('supervisor'))
            <div>
                <select wire:model.live="filtroUsuario"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los vendedores</option>
                    @foreach($vendedores as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Monto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($cotizaciones as $cot)
                <tr wire:key="cot-{{ $cot->id }}" class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $cot->codigo }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $cot->cliente_id) }}"
                           class="font-medium text-indigo-700 hover:text-indigo-900">
                            {{ $cot->cliente->razon_social }}
                        </a>
                        @if($cot->campana)
                        <p class="text-xs text-gray-400">{{ $cot->campana->nombre }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $cot->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        S/ {{ number_format($cot->monto_total, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        @if($editandoId === $cot->id)
                        {{-- Inline estado editor --}}
                        <div class="space-y-2">
                            <select wire:model.live="nuevoEstado"
                                    class="w-full rounded border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($estados as $e)
                                <option value="{{ $e->value }}">{{ $e->label() }}</option>
                                @endforeach
                            </select>
                            @if(in_array($nuevoEstado, ['aprobada', 'rechazada']))
                            <input wire:model="fecha_respuesta" type="date"
                                   placeholder="Fecha respuesta"
                                   class="w-full rounded border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('fecha_respuesta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            @endif
                            <div class="flex gap-1">
                                <button wire:click="guardarEstado"
                                        class="px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    Guardar
                                </button>
                                <button wire:click="cancelarEdicion"
                                        class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded hover:bg-gray-200">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                        @else
                        <button wire:click="abrirCambioEstado({{ $cot->id }})"
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $cot->estado->color() }} hover:opacity-80 transition-opacity">
                            {{ $cot->estado->label() }}
                        </button>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $cot->usuario->name }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('cotizaciones.imprimir', $cot->id) }}" target="_blank"
                               class="text-xs text-gray-500 hover:text-gray-700 font-medium inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                PDF
                            </a>
                            <a href="{{ route('clientes.show', $cot->cliente_id) }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Ver cliente
                            </a>
                            <button
                                x-data
                                x-on:click="if(confirm('¿Eliminar la cotización {{ $cot->codigo }}?')) $wire.eliminar({{ $cot->id }})"
                                class="text-gray-300 hover:text-red-500 transition-colors"
                                title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                        No hay cotizaciones registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($cotizaciones->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $cotizaciones->links() }}</div>
        @endif
    </div>
</div>
