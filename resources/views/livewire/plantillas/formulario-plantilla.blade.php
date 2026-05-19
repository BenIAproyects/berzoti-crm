<div>
    <form wire:submit="guardar" class="space-y-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Datos de la plantilla</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre interno <span class="text-red-500">*</span></label>
                    <input wire:model="nombre" type="text" placeholder="Ej: Presentación panetones 2026"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="flex items-center gap-3 h-10">
                        <input wire:model="activo" type="checkbox" id="activo"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="activo" class="text-sm text-gray-700">Plantilla activa</label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asunto del correo <span class="text-red-500">*</span></label>
                    <input wire:model="asunto" type="text"
                           placeholder="Ej: Propuesta comercial panetones @{{razon_social}}"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('asunto') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Variables disponibles --}}
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide mb-2">Variables disponibles (copiar y pegar en el cuerpo)</p>
            <div class="flex flex-wrap gap-2">
                @foreach($variables as $var)
                <code class="px-2 py-1 bg-white border border-indigo-200 rounded text-xs text-indigo-700 font-mono cursor-pointer select-all"
                      title="Clic para seleccionar">{{ $var }}</code>
                @endforeach
            </div>
        </div>

        {{-- Cuerpo del correo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-medium text-gray-700">Cuerpo del correo (HTML o texto) <span class="text-red-500">*</span></label>
                <button type="button" wire:click="togglePreview"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium underline">
                    {{ $mostrarPreview ? 'Ocultar preview' : 'Ver preview' }}
                </button>
            </div>

            <textarea wire:model.live="cuerpo_html" rows="14"
                      placeholder="Hola @{{contacto_principal}},&#10;&#10;Le escribimos de parte de Berzoti para presentarle..."
                      class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono"></textarea>
            @error('cuerpo_html') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            @if($mostrarPreview)
            <div class="mt-4 border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-500 border-b border-gray-200">
                    PREVIEW (con datos de ejemplo)
                </div>
                <iframe srcdoc="{{ e($this->previewHtml) }}"
                        class="w-full border-0"
                        style="height:500px;"
                        sandbox="allow-same-origin"></iframe>
            </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('plantillas.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                <span wire:loading.remove>{{ $modoEdicion ? 'Guardar cambios' : 'Crear plantilla' }}</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>

    </form>
</div>
