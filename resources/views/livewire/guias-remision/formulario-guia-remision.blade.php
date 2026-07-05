<div>
    @if(!$mostrar)
    <button wire:click="$set('mostrar', true)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva guía de remisión
    </button>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-700">
                {{ $modoEdicion ? 'Editar guía de remisión' : 'Nueva guía de remisión' }}
            </h3>
            <button wire:click="cancelar" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="guardar" class="space-y-5">

            {{-- Fila 1: N° Guía, Fecha emisión, Fecha entrega --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">N° Guía de remisión</label>
                    <input wire:model="numero_guia" type="text" placeholder="Ej: T001-00001"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('numero_guia') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de emisión <span class="text-red-500">*</span></label>
                    <input wire:model="fecha_emision" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_emision') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de entrega</label>
                    <input wire:model="fecha_entrega" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Fila 2: OC, Factura, Estado --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">OC vinculada</label>
                    <select wire:model.live="orden_compra_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin orden de compra</option>
                        @foreach($ordenes_compra as $oc)
                        <option value="{{ $oc->id }}">{{ $oc->codigo }}{{ $oc->numero_oc ? ' · '.$oc->numero_oc : '' }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">Al seleccionar una OC se cargan los ítems automáticamente.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Factura vinculada</label>
                    <select wire:model.live="factura_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin factura</option>
                        @foreach($facturas as $fac)
                        <option value="{{ $fac->id }}">{{ $fac->codigo }}{{ $fac->numero_factura ? ' · '.$fac->numero_factura : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado de entrega</label>
                    <select wire:model="estado_entrega"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($estados as $e)
                        <option value="{{ $e->value }}">{{ $e->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Dirección de entrega --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dirección de entrega</label>
                <input wire:model="direccion_entrega" type="text"
                       placeholder="Dirección de entrega..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Ítems --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-600">Productos a entregar <span class="text-red-500">*</span></label>
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
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 min-w-[160px]">Producto</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 min-w-[140px]">Descripción</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 w-24">Cant. enviada</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 min-w-[140px]">Observaciones</th>
                                <th class="px-3 py-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($items as $i => $item)
                            <tr wire:key="gr-item-{{ $i }}">
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.producto"
                                           type="text" placeholder="Ej: Panetón 900g..."
                                           class="w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    @error("items.{$i}.producto") <p class="text-red-500 mt-0.5">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.descripcion"
                                           type="text" placeholder="Detalle..."
                                           class="w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                </td>
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.cantidad_enviada"
                                           type="number" step="1" min="1" placeholder="1"
                                           class="w-full border-0 bg-transparent text-sm text-center focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
                                    @error("items.{$i}.cantidad_enviada") <p class="text-red-500 mt-0.5 text-center">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-2 py-1.5">
                                    <input wire:model="items.{{ $i }}.observaciones"
                                           type="text" placeholder="Notas..."
                                           class="w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none placeholder-gray-300 p-0">
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
                    </table>
                </div>
                @error('items') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Observaciones generales</label>
                <textarea wire:model="observaciones" rows="2"
                          placeholder="Instrucciones especiales de entrega, responsable, etc."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="cancelar"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    <span wire:loading.remove wire:target="guardar">{{ $modoEdicion ? 'Guardar cambios' : 'Registrar guía' }}</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
