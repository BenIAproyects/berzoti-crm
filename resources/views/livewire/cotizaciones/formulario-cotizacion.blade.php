<div>
    @if(!$mostrar)
    <button wire:click="$set('mostrar', true)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva cotización
    </button>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-700">
                {{ $modoEdicion ? 'Editar cotización' : 'Nueva cotización' }}
            </h3>
            <button wire:click="cancelar" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="guardar" class="space-y-5">

            {{-- Fila 1: Fecha, Estado, Campaña --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha <span class="text-red-500">*</span></label>
                    <input wire:model="fecha" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado</label>
                    <select wire:model.live="estado"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($estados as $e)
                        <option value="{{ $e->value }}">{{ $e->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Campaña</label>
                    <select wire:model="campana_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin campaña</option>
                        @foreach($campanas as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fila 2: Fechas envío / respuesta --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de envío</label>
                    <input wire:model="fecha_envio" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Fecha de respuesta
                        @if(in_array($estado, ['aprobada', 'rechazada']))
                        <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <input wire:model="fecha_respuesta" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_respuesta') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @if(in_array($estado, ['aprobada', 'rechazada']) && !$fecha_respuesta)
            <div class="flex items-center gap-2 text-xs text-amber-700 bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Para marcar como {{ $estado }}, debes ingresar la fecha de respuesta.
            </div>
            @endif

            {{-- Ítems / Productos --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-600">Productos / Ítems <span class="text-red-500">*</span></label>
                    <button type="button" wire:click="agregarItem"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar ítem
                    </button>
                </div>

                <div class="rounded-lg border border-gray-200 overflow-hidden">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Descripción</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 w-32">Cant.</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 w-28">Precio unit.</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 w-24">Subtotal</th>
                                <th class="px-3 py-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($items as $i => $item)
                            <tr wire:key="item-{{ $i }}">
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.descripcion"
                                           type="text" placeholder="Descripción del producto..."
                                           class="w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    @error("items.{$i}.descripcion") <p class="text-red-500 mt-0.5">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.cantidad"
                                           wire:change="calcularItem({{ $i }})"
                                           type="number" step="0.01" min="0.01" placeholder="1"
                                           class="w-full border-0 bg-transparent text-sm text-center focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    @error("items.{$i}.cantidad") <p class="text-red-500 mt-0.5 text-center">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-2 py-1.5">
                                    <div class="flex items-center gap-1">
                                        <span class="text-gray-400">S/</span>
                                        <input wire:model="items.{{ $i }}.precio_unitario"
                                               wire:change="calcularItem({{ $i }})"
                                               type="number" step="0.01" min="0" placeholder="0.00"
                                               class="w-full border-0 bg-transparent text-sm text-right focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    </div>
                                    @error("items.{$i}.precio_unitario") <p class="text-red-500 mt-0.5">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-1.5 text-right font-semibold text-gray-700">
                                    S/ {{ number_format((float)($item['subtotal'] ?? 0), 2) }}
                                </td>
                                <td class="px-2 py-1.5 text-center">
                                    @if(count($items) > 1)
                                    <button type="button" wire:click="quitarItem({{ $i }})"
                                            class="text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-right text-sm font-semibold text-gray-700">Total:</td>
                                <td class="px-3 py-2 text-right text-sm font-bold text-indigo-700">
                                    S/ {{ number_format((float)$monto_total, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @error('items') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Observaciones</label>
                <textarea wire:model="observaciones" rows="2"
                          placeholder="Condiciones, descuentos, notas adicionales..."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="cancelar"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    <span wire:loading.remove wire:target="guardar">{{ $modoEdicion ? 'Guardar cambios' : 'Crear cotización' }}</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
