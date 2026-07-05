<div>
    @if(!$mostrar)
    <button wire:click="$set('mostrar', true)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Registrar pago
    </button>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-green-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-700">
                {{ $modoEdicion ? 'Editar pago' : 'Registrar pago' }}
            </h3>
            <button wire:click="cancelar" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="guardar" class="space-y-5">

            {{-- Selector de cliente (solo en modo global) --}}
            @if(!$cliente)
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cliente <span class="text-red-500">*</span></label>
                <select wire:model.live="clienteSelId"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Seleccionar cliente...</option>
                    @foreach($clientes as $c)
                    <option value="{{ $c->id }}">{{ $c->razon_social }}{{ $c->ruc ? ' — '.$c->ruc : '' }}</option>
                    @endforeach
                </select>
                @error('clienteSelId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            @endif

            {{-- Factura --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Factura <span class="text-red-500">*</span></label>
                <select wire:model.live="factura_id"
                        @if(!$cliente && !$clienteSelId) disabled @endif
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Seleccionar factura pendiente...</option>
                    @foreach($facturas as $fac)
                    <option value="{{ $fac->id }}">
                        {{ $fac->codigo }}{{ $fac->numero_factura ? ' · '.$fac->numero_factura : '' }}
                        — Saldo: S/ {{ number_format($fac->saldo_pendiente, 2) }}
                        ({{ $fac->estado_pago->label() }})
                    </option>
                    @endforeach
                </select>
                @error('factura_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                {{-- Resumen de la factura seleccionada --}}
                @if($infoTotal)
                <div class="mt-2 flex items-center gap-6 bg-gray-50 rounded-lg px-4 py-2.5 text-xs">
                    <div class="text-gray-500">
                        Total factura: <span class="font-semibold text-gray-700">S/ {{ $infoTotal }}</span>
                    </div>
                    <div class="text-gray-500">
                        Ya pagado: <span class="font-semibold text-green-600">S/ {{ $infoYaPagado }}</span>
                    </div>
                    <div class="text-gray-500">
                        Saldo pendiente: <span class="font-semibold text-red-600">S/ {{ $infoSaldo }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Fila 1: Fecha, Monto --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de pago <span class="text-red-500">*</span></label>
                    <input wire:model="fecha_pago" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('fecha_pago') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Monto pagado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-green-500 focus-within:border-green-500">
                        <span class="text-gray-400 text-sm">S/</span>
                        <input wire:model="monto_pagado" type="number" step="0.01" min="0.01"
                               class="flex-1 border-0 bg-transparent text-sm font-semibold focus:ring-0 focus:outline-none text-right p-0">
                    </div>
                    @error('monto_pagado') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Fila 2: Método de pago --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Método de pago</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($metodos as $metodo)
                    <label class="cursor-pointer">
                        <input wire:model.live="metodo_pago" type="radio" value="{{ $metodo->value }}" class="sr-only peer">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                            peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600
                            bg-white text-gray-600 border-gray-300 hover:border-green-400">
                            {{ $metodo->label() }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Banco / N° Operación (condicional) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Banco / Entidad</label>
                    <input wire:model="banco" type="text" placeholder="Ej: BCP, Interbank..."
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">N° de operación / referencia</label>
                    <input wire:model="numero_operacion" type="text" placeholder="Ej: 1234567890"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Observaciones</label>
                <textarea wire:model="observaciones" rows="2"
                          placeholder="Notas adicionales sobre el pago..."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="cancelar"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <span wire:loading.remove wire:target="guardar">{{ $modoEdicion ? 'Guardar cambios' : 'Registrar pago' }}</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
