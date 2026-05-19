<div>
    {{-- Botón disparador --}}
    @if(!$mostrarModal)
    <button wire:click="abrirModal"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
        Enviar correo
    </button>
    @endif

    {{-- Modal --}}
    @if($mostrarModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Enviar correo a {{ $cliente->contacto_principal }}</h3>
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div class="text-sm text-gray-500">
                    Destinatario: <strong class="text-gray-800">{{ $cliente->correo ?? '⚠ Sin correo registrado' }}</strong>
                </div>

                @if(!$cliente->correo)
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    Este cliente no tiene correo registrado. Edita su ficha antes de enviar.
                </div>
                @else

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plantilla <span class="text-red-500">*</span></label>
                    <select wire:model.live="plantilla_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecciona una plantilla...</option>
                        @foreach($plantillas as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    @error('plantilla_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asociar a campaña (opcional)</label>
                    <select wire:model="campana_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin campaña</option>
                        @foreach($campanas as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                @if($previewHtml)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-500 border-b">PREVIEW DEL MENSAJE</div>
                    <iframe srcdoc="{{ e($previewHtml) }}"
                            class="w-full border-0"
                            style="height:320px;"
                            sandbox="allow-same-origin"></iframe>
                </div>
                @endif

                @endif
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="cerrarModal"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                @if($cliente->correo)
                <button wire:click="enviar"
                        wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="enviar">Enviar</span>
                    <span wire:loading wire:target="enviar">Enviando...</span>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
