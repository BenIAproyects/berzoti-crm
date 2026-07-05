<div>
    @if(!$mostrar)
    <button wire:click="$set('mostrar', true)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva factura
    </button>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-700">
                {{ $modoEdicion ? 'Editar factura' : 'Nueva factura' }}
            </h3>
            <button wire:click="cancelar" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="guardar" class="space-y-5">

            {{-- Fila 1: N° Factura, Fecha emisión, Fecha vencimiento --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">N° Factura</label>
                    <input wire:model="numero_factura" type="text" placeholder="Ej: F001-00001"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('numero_factura') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de emisión <span class="text-red-500">*</span></label>
                    <input wire:model="fecha_emision" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_emision') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de vencimiento</label>
                    <input wire:model="fecha_vencimiento" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_vencimiento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Fila 2: OC vinculada, Estado de pago --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">OC vinculada</label>
                    <select wire:model.live="orden_compra_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin orden de compra</option>
                        @foreach($ordenes_compra as $oc)
                        <option value="{{ $oc->id }}">
                            {{ $oc->codigo }}{{ $oc->numero_oc ? ' · ' . $oc->numero_oc : '' }} — S/ {{ number_format($oc->total, 2) }}
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">Al seleccionar una OC, los montos se pre-cargan automáticamente.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado de pago</label>
                    <select wire:model="estado_pago"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($estados as $e)
                        <option value="{{ $e->value }}">{{ $e->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fila 3: Montos --}}
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Montos</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subtotal (s/IGV) <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                            <span class="text-gray-400 text-sm">S/</span>
                            <input wire:model.live.debounce.400ms="subtotal" type="number" step="0.01" min="0"
                                   class="flex-1 border-0 bg-transparent text-sm focus:ring-0 focus:outline-none text-right p-0">
                        </div>
                        @error('subtotal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">IGV (18%)</label>
                        <div class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-indigo-500">
                            <span class="text-gray-400 text-sm">S/</span>
                            <input wire:model="igv" type="number" step="0.01" min="0"
                                   class="flex-1 border-0 bg-transparent text-sm focus:ring-0 focus:outline-none text-right p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total</label>
                        <div class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-indigo-500">
                            <span class="text-gray-400 text-sm">S/</span>
                            <input wire:model.live="total" type="number" step="0.01" min="0"
                                   class="flex-1 border-0 bg-transparent text-sm font-semibold focus:ring-0 focus:outline-none text-right p-0">
                        </div>
                        @error('total') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Fila 4: Pago --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Monto pagado</label>
                    <div class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-indigo-500">
                        <span class="text-gray-400 text-sm">S/</span>
                        <input wire:model.live.debounce.400ms="monto_pagado" type="number" step="0.01" min="0"
                               class="flex-1 border-0 bg-transparent text-sm focus:ring-0 focus:outline-none text-right p-0">
                    </div>
                    @error('monto_pagado') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Saldo pendiente</label>
                    <div class="flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                        <span class="text-gray-400 text-sm">S/</span>
                        <span class="flex-1 text-sm font-semibold text-right {{ (float)$saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format((float)$saldo_pendiente, 2) }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Calculado automáticamente: Total − Monto pagado</p>
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Observaciones</label>
                <textarea wire:model="observaciones" rows="2"
                          placeholder="Condiciones de pago, notas adicionales..."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="cancelar"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    <span wire:loading.remove wire:target="guardar">{{ $modoEdicion ? 'Guardar cambios' : 'Registrar factura' }}</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
