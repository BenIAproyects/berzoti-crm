<div class="space-y-6">

    {{-- Paso: Subir archivo --}}
    @if($paso === 'subir')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Importar clientes desde Excel</h2>
                <p class="text-sm text-gray-500 mt-1">Solo se aceptan archivos .xlsx, .xls o .csv. Máximo 5 MB.</p>
            </div>
            <a href="{{ route('clientes.template') }}"
               class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Descargar template
            </a>
        </div>

        {{-- Zona de upload --}}
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:border-indigo-400 transition-colors"
             x-data="{ dragging: false }"
             @dragover.prevent="dragging = true"
             @dragleave="dragging = false"
             @drop.prevent="dragging = false">

            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>

            <label class="cursor-pointer">
                <span class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Selecciona tu archivo</span>
                <span class="text-sm text-gray-500"> o arrástralo aquí</span>
                <input wire:model="archivo" type="file" accept=".xlsx,.xls,.csv" class="hidden">
            </label>
            <p class="text-xs text-gray-400 mt-2">Columnas requeridas: razon_social, contacto_principal</p>
        </div>

        <div wire:loading wire:target="archivo" class="mt-3 text-sm text-indigo-600 text-center">
            Analizando archivo...
        </div>

        @if($error)
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {{ $error }}
        </div>
        @endif

        @error('archivo')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    @endif

    {{-- Paso: Preview --}}
    @if($paso === 'preview')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Vista previa del archivo</h2>
            <button wire:click="reiniciar" class="text-sm text-gray-500 hover:text-red-600 underline">
                Cancelar
            </button>
        </div>

        <p class="text-sm text-gray-500 mb-4">
            Se muestran hasta 5 filas de muestra. Verifica que los datos sean correctos antes de importar.
        </p>

        @if(count($preview) > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
            <table class="min-w-full text-xs divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Razón social</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">RUC</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Contacto</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Correo</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Tipo</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Departamento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($preview as $fila)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-800 max-w-[180px] truncate">{{ $fila[0] ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $fila[2] ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $fila[5] ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $fila[9] ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $fila[3] ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $fila[12] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-700 mb-6">
            <strong>Reglas de duplicidad:</strong> Se omitirá un cliente si ya existe con el mismo RUC, el mismo correo principal, o la misma razón social + contacto principal.
        </div>

        <div class="flex justify-end gap-3">
            <button wire:click="reiniciar"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cambiar archivo
            </button>
            <button wire:click="confirmarImportacion"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="confirmarImportacion">Importar ahora</span>
                <span wire:loading wire:target="confirmarImportacion">Importando...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Paso: Resultado --}}
    @if($paso === 'resultado' && $resultado)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-6">Resultado de la importación</h2>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-green-700">{{ $resultado['importadas'] }}</p>
                <p class="text-sm text-green-600 mt-1">Importadas</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-yellow-700">{{ $resultado['duplicadas'] }}</p>
                <p class="text-sm text-yellow-600 mt-1">Duplicadas omitidas</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-red-700">{{ $resultado['errores'] }}</p>
                <p class="text-sm text-red-600 mt-1">Con errores</p>
            </div>
        </div>

        @if(count($resultado['detalle']) > 0)
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Detalle de omisiones y errores</p>
            <ul class="space-y-1 max-h-48 overflow-y-auto">
                @foreach($resultado['detalle'] as $linea)
                <li class="text-xs text-gray-600">{{ $linea }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex justify-end gap-3">
            <button wire:click="reiniciar"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Importar otro archivo
            </button>
            <a href="{{ route('clientes.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Ver clientes
            </a>
        </div>
    </div>
    @endif

    {{-- Historial de importaciones --}}
    @if(count($historial) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Historial de importaciones</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100">
                <thead>
                    <tr>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-500">Fecha</th>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-500">Archivo</th>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-500">Usuario</th>
                        <th class="pb-2 text-right text-xs font-semibold text-gray-500">Importadas</th>
                        <th class="pb-2 text-right text-xs font-semibold text-gray-500">Duplicadas</th>
                        <th class="pb-2 text-right text-xs font-semibold text-gray-500">Errores</th>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-500">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($historial as $h)
                    <tr>
                        <td class="py-2 text-gray-500 text-xs">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 text-gray-700 max-w-[180px] truncate">{{ $h->archivo }}</td>
                        <td class="py-2 text-gray-600">{{ $h->usuario->name }}</td>
                        <td class="py-2 text-right text-green-700 font-medium">{{ $h->total_importadas }}</td>
                        <td class="py-2 text-right text-yellow-700">{{ $h->total_duplicadas }}</td>
                        <td class="py-2 text-right text-red-700">{{ $h->total_error }}</td>
                        <td class="py-2">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $h->estado === 'completado' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $h->estado === 'completado' ? 'Completado' : 'Con errores' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
