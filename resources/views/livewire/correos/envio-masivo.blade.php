<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-base font-semibold text-gray-700 mb-4">Envío masivo de correos</h3>

    <div class="grid grid-cols-2 gap-4 mb-5">
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-indigo-700">{{ $conCorreo }}</p>
            <p class="text-xs text-indigo-500 mt-1">Clientes con correo</p>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $sinCorreo }}</p>
            <p class="text-xs text-amber-500 mt-1">Sin correo (se omitirán)</p>
        </div>
    </div>

    @if($resultado)
    <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm font-semibold text-green-800 mb-1">Envío programado correctamente</p>
        <p class="text-sm text-green-700">
            <strong>{{ $resultado['enviados'] }}</strong> correo(s) en cola &bull;
            <strong>{{ $resultado['omitidos'] }}</strong> omitido(s) (ya enviados con esta plantilla)
        </p>
        <p class="text-xs text-green-600 mt-1">Los correos se están enviando en segundo plano.</p>
    </div>
    @endif

    @if(!$confirmar)
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Plantilla a enviar <span class="text-red-500">*</span></label>
            <select wire:model="plantilla_id"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Selecciona una plantilla...</option>
                @foreach($plantillas as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }} — {{ Str::limit($p->asunto, 50) }}</option>
                @endforeach
            </select>
            @error('plantilla_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="text-sm text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200">
            Se enviarán correos a los <strong>{{ $conCorreo }}</strong> clientes activos con correo registrado.
            Los que ya recibieron esta plantilla en esta campaña serán omitidos automáticamente.
        </div>

        <button wire:click="preparar"
                @if($conCorreo === 0) disabled @endif
                class="w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed">
            Preparar envío masivo
        </button>
    </div>
    @else
    <div class="space-y-4">
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-sm font-semibold text-amber-800">¿Confirmas el envío masivo?</p>
            <p class="text-sm text-amber-700 mt-1">
                Se enviarán correos a <strong>{{ $conCorreo }}</strong> clientes en segundo plano.
                Esta acción no se puede deshacer.
            </p>
        </div>
        <div class="flex gap-3">
            <button wire:click="cancelar"
                    class="flex-1 px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancelar
            </button>
            <button wire:click="enviarTodos"
                    wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="enviarTodos">Sí, enviar ahora</span>
                <span wire:loading wire:target="enviarTodos">Programando...</span>
            </button>
        </div>
    </div>
    @endif
</div>
