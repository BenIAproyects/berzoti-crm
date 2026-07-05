<div>
    @if(!$mostrar)
    <button wire:click="$set('mostrar', true)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Registrar seguimiento
    </button>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-700">Nuevo seguimiento</h3>
            <button wire:click="$set('mostrar', false)" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="guardar" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select wire:model="tipo" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($tipos as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </select>
                    @error('tipo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora <span class="text-red-500">*</span></label>
                    <input wire:model="fecha_hora" type="datetime-local"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_hora') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Campaña (opcional)</label>
                    <select wire:model="campana_id" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin campaña</option>
                        @foreach($campanas as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">¿Qué se hizo? <span class="text-red-500">*</span></label>
                <textarea wire:model="detalle" rows="3" placeholder="Describe la acción realizada..."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                @error('detalle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">¿Qué pasó? (resultado)</label>
                <textarea wire:model="resultado" rows="2" placeholder="Respuesta del cliente, acuerdo, observación..."
                          class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cambiar estado comercial</label>
                    <select wire:model="estado_comercial_nuevo" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin cambio</option>
                        @foreach($estados as $e)
                        <option value="{{ $e->value }}">{{ $e->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">¿Qué sigue?</label>
                    <input wire:model="proxima_accion" type="text" placeholder="Ej: Llamar para confirmar pedido"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('proxima_accion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha próxima acción</label>
                    <input wire:model="fecha_proxima_accion" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('fecha_proxima_accion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @if($proxima_accion && $fecha_proxima_accion)
            <div class="text-xs text-indigo-600 bg-indigo-50 px-3 py-2 rounded-lg">
                Se creará una tarea automáticamente para el {{ \Carbon\Carbon::parse($fecha_proxima_accion)->format('d/m/Y') }}
            </div>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" wire:click="$set('mostrar', false)"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    <span wire:loading.remove>Guardar seguimiento</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
