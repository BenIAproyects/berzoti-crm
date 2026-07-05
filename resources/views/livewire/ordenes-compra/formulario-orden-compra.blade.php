<div>
    @if(!$mostrar)
    <button wire:click="$set('mostrar', true)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva OC
    </button>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-700">
                {{ $modoEdicion ? 'Editar orden de compra' : 'Nueva orden de compra' }}
            </h3>
            <button wire:click="cancelar" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="guardar" class="space-y-5">

            {{-- Fila 1: N° OC, Fecha OC, Fecha recepción --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">N° OC del cliente</label>
                    <input wire:model="numero_oc" type="text" placeholder="Ej: OC-2025-001"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('numero_oc') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha OC <span class="text-red-500">*</span></label>
                    <input wire:model="fecha_oc" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_oc') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de recepción</label>
                    <input wire:model="fecha_recepcion" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Fila 2: Estado, Campaña, Cotización vinculada --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado</label>
                    <select wire:model="estado"
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
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cotización vinculada</label>
                    <select wire:model="cotizacion_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin cotización</option>
                        @foreach($cotizaciones as $cot)
                        <option value="{{ $cot->id }}">{{ $cot->codigo }} — S/ {{ number_format($cot->monto_total, 2) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Ítems --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-600">Productos / Ítems <span class="text-red-500">*</span></label>
                    <button type="button" wire:click="agregarItem"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar ítem
                    </button>
                </div>

                <div class="rounded-lg border border-gray-200 overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 min-w-[160px]">Producto</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 min-w-[160px]">Descripción</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 w-24">Cantidad</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 w-28">P. Unit. (s/IGV)</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 w-24">Subtotal</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 w-20">IGV 18%</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 w-24">Total</th>
                                <th class="px-3 py-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($items as $i => $item)
                            <tr wire:key="oc-item-{{ $i }}">
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.producto"
                                           type="text" placeholder="Ej: Panetón 900g..."
                                           class="w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    @error("items.{$i}.producto") <p class="text-red-500 mt-0.5">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.descripcion"
                                           type="text" placeholder="Detalle adicional..."
                                           class="w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                </td>
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.cantidad_pedida"
                                           wire:change="calcularItem({{ $i }})"
                                           type="number" step="1" min="1" placeholder="1"
                                           class="w-full border-0 bg-transparent text-sm text-center focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    @error("items.{$i}.cantidad_pedida") <p class="text-red-500 mt-0.5 text-center">{{ $message }}</p> @enderror
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
                                <td class="px-3 py-1.5 text-right text-gray-600">
                                    S/ {{ number_format((float)($item['subtotal'] ?? 0), 2) }}
                                </td>
                                <td class="px-3 py-1.5 text-right text-gray-500">
                                    S/ {{ number_format((float)($item['igv'] ?? 0), 2) }}
                                </td>
                                <td class="px-3 py-1.5 text-right font-semibold text-gray-700">
                                    S/ {{ number_format((float)($item['total'] ?? 0), 2) }}
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
                        <tfoot class="bg-gray-50 border-t border-gray-200 text-xs">
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-600">Subtotal (s/IGV):</td>
                                <td class="px-3 py-2 text-right text-gray-700 font-semibold">S/ {{ number_format((float)$subtotal, 2) }}</td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-600">IGV (18%):</td>
                                <td colspan="3" class="px-3 py-2 text-right text-gray-600">S/ {{ number_format((float)$igv, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-right font-bold text-gray-800">Total:</td>
                                <td colspan="3" class="px-3 py-2 text-right font-bold text-indigo-700 text-sm">S/ {{ number_format((float)$total, 2) }}</td>
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
                          placeholder="Condiciones de entrega, plazo, notas..."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="cancelar"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    <span wire:loading.remove wire:target="guardar">{{ $modoEdicion ? 'Guardar cambios' : 'Registrar OC' }}</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
