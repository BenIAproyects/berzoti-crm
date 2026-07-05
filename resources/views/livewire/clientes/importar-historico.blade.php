<div class="space-y-6">

    {{-- =====================================================================
         PASO 1: SUBIR ARCHIVO
    ====================================================================== --}}
    @if($paso === 'subir')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h2 class="text-base font-semibold text-gray-800">Importación histórica — Base Normalizada</h2>
            <p class="text-sm text-gray-500 mt-1">
                Sube el archivo <strong>Base_Normalizada_v5_Sistema_Ventas.xlsx</strong> (o equivalente).
                El sistema procesará automáticamente las hojas: Clientes, Contactos, Correos, Teléfonos y Observaciones.
            </p>
        </div>

        {{-- Info de hojas --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            @foreach([
                ['Clientes','2,287','bg-indigo-50 border-indigo-200 text-indigo-700'],
                ['Contactos','749','bg-blue-50 border-blue-200 text-blue-700'],
                ['Correos','4,868','bg-emerald-50 border-emerald-200 text-emerald-700'],
                ['Teléfonos','5,123','bg-amber-50 border-amber-200 text-amber-700'],
                ['Observaciones','1,145','bg-purple-50 border-purple-200 text-purple-700'],
            ] as [$nombre, $aprox, $color])
            <div class="border rounded-lg p-3 text-center {{ $color }}">
                <p class="text-xs font-semibold">{{ $nombre }}</p>
                <p class="text-lg font-bold">~{{ $aprox }}</p>
                <p class="text-xs opacity-75">registros</p>
            </div>
            @endforeach
        </div>

        {{-- Zona de upload --}}
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:border-indigo-400 transition-colors">
            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <label class="cursor-pointer">
                <span class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Selecciona el archivo Excel</span>
                <span class="text-sm text-gray-500"> o arrástralo aquí</span>
                <input wire:model="archivo" type="file" accept=".xlsx,.xls" class="hidden">
            </label>
            <p class="text-xs text-gray-400 mt-2">Solo .xlsx / .xls — Máximo 20 MB</p>
        </div>

        <div wire:loading wire:target="archivo" class="mt-4 flex items-center gap-2 text-sm text-indigo-600">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Analizando archivo...
        </div>

        @if($error)
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{{ $error }}</div>
        @endif
        @error('archivo')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    @endif

    {{-- =====================================================================
         PASO 2: CONFIGURAR + PREVIEW
    ====================================================================== --}}
    @if($paso === 'configurar')
    <div class="space-y-4">

        {{-- Cabecera --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Configurar importación</h2>
                <p class="text-sm text-gray-500 mt-0.5">Archivo: <span class="font-medium">{{ $archivoNombre }}</span></p>
            </div>
            <button wire:click="reiniciar" class="text-sm text-gray-500 hover:text-red-600 underline">
                Cambiar archivo
            </button>
        </div>

        {{-- Selección de hojas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">¿Qué hojas deseas importar?</h3>
            <div class="space-y-3">

                @php
                    $hojas = [
                        ['prop' => 'importarClientes',      'key' => 'clientes',      'label' => 'Maestro de Clientes',      'desc' => 'Crea los registros principales de cada empresa'],
                        ['prop' => 'importarContactos',     'key' => 'contactos',     'label' => 'Contactos',                'desc' => 'Personas de contacto por empresa'],
                        ['prop' => 'importarCorreos',       'key' => 'correos',       'label' => 'Correos electrónicos',     'desc' => 'Todos los emails por empresa'],
                        ['prop' => 'importarTelefonos',     'key' => 'telefonos',     'label' => 'Teléfonos',                'desc' => 'Celulares y fijos por empresa'],
                        ['prop' => 'importarObservaciones', 'key' => 'observaciones', 'label' => 'Observaciones (→ Seguimientos)', 'desc' => 'Gestiones históricas como seguimientos'],
                    ];
                @endphp

                @foreach($hojas as $hoja)
                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                    <input wire:model.live="{{ $hoja['prop'] }}" type="checkbox"
                           class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800">{{ $hoja['label'] }}</span>
                            @if(isset($statsPreview[$hoja['key']]))
                                <span class="text-xs text-indigo-600 font-semibold">
                                    {{ number_format($statsPreview[$hoja['key']]['total']) }} registros
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $hoja['desc'] }}</p>
                    </div>
                </label>
                @endforeach

            </div>
        </div>

        {{-- Preview por hoja seleccionada --}}
        @foreach($hojas as $hoja)
            @if(${'importar'.ucfirst(str_replace('importar', '', $hoja['prop']))} ?? false)
            @php $key = $hoja['key']; $stat = $statsPreview[$key] ?? null; @endphp
            @if($stat)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700">{{ $hoja['label'] }}</h4>
                    <span class="text-xs text-gray-500">{{ number_format($stat['total']) }} registros</span>
                </div>

                @if($key === 'clientes' && ($stat['sospechosas'] ?? 0) > 0)
                <div class="px-5 py-2 bg-amber-50 border-b border-amber-200 text-xs text-amber-700">
                    ⚠ <strong>{{ $stat['sospechosas'] }}</strong> registros con cantidades sospechosas
                    (posiblemente montos en soles). Serán marcados como <em>requiere validación</em> y
                    no se usarán en reportes hasta que sean confirmados.
                </div>
                @endif

                @if(count($stat['muestra'] ?? []) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach(array_slice($stat['columnas'], 1, 6) as $col)
                                <th class="px-3 py-2 text-left font-semibold text-gray-500 whitespace-nowrap">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($stat['muestra'] as $fila)
                            <tr class="hover:bg-gray-50">
                                @foreach(array_slice($fila, 1, 6) as $celda)
                                <td class="px-3 py-2 text-gray-600 max-w-[160px] truncate">{{ $celda ?? '—' }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            @endif
            @endif
        @endforeach

        {{-- Advertencias generales --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
            <p class="font-semibold mb-1">Reglas de importación</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                <li>Los clientes se identifican por <strong>RUC</strong>. Si el RUC ya existe en el sistema, la fila se omite.</li>
                <li>Contactos, correos y teléfonos solo se importan si su RUC existe en el maestro o ya está en el sistema.</li>
                <li>Las observaciones se crean como <strong>Seguimientos</strong> de tipo "Observación".</li>
                <li>Cantidades &gt; 99,999 o con decimales se marcan como <em>requiere validación</em> y no aparecerán en reportes.</li>
                <li>La importación puede tardar varios minutos para archivos grandes. No cierres la página.</li>
            </ul>
        </div>

        @if($error)
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{{ $error }}</div>
        @endif

        <div class="flex justify-end gap-3">
            <button wire:click="reiniciar"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancelar
            </button>
            <button wire:click="ejecutarImportacion"
                    wire:loading.attr="disabled"
                    wire:confirm="¿Confirmas iniciar la importación? Este proceso puede tardar varios minutos."
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="ejecutarImportacion">Importar ahora</span>
                <span wire:loading wire:target="ejecutarImportacion" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Importando... no cierres la página
                </span>
            </button>
        </div>
    </div>
    @endif

    {{-- =====================================================================
         PASO 3: RESULTADO
    ====================================================================== --}}
    @if($paso === 'resultado' && $resultados)
    <div class="space-y-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-800">Importación completada</h2>
            </div>
            <p class="text-sm text-gray-500">Archivo: <span class="font-medium">{{ $archivoNombre }}</span></p>
        </div>

        @php
            $labels = [
                'clientes'      => 'Maestro de Clientes',
                'contactos'     => 'Contactos',
                'correos'       => 'Correos',
                'telefonos'     => 'Teléfonos',
                'observaciones' => 'Observaciones → Seguimientos',
            ];
        @endphp

        @foreach($resultados as $hoja => $res)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700">{{ $labels[$hoja] ?? $hoja }}</h4>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-green-700">{{ number_format($res['importadas'] ?? 0) }}</p>
                        <p class="text-xs text-green-600 mt-1">Importados</p>
                    </div>
                    @if(isset($res['duplicadas']))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-yellow-700">{{ number_format($res['duplicadas']) }}</p>
                        <p class="text-xs text-yellow-600 mt-1">Duplicados omitidos</p>
                    </div>
                    @elseif(isset($res['omitidas']))
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-gray-600">{{ number_format($res['omitidas']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Omitidos</p>
                    </div>
                    @endif
                    @if(isset($res['validar']) && $res['validar'] > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-amber-700">{{ number_format($res['validar']) }}</p>
                        <p class="text-xs text-amber-600 mt-1">Requieren validación</p>
                    </div>
                    @endif
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-red-700">{{ number_format($res['errores'] ?? 0) }}</p>
                        <p class="text-xs text-red-600 mt-1">Errores</p>
                    </div>
                </div>

                @if(count($res['detalle'] ?? []) > 0)
                <details class="mt-2">
                    <summary class="text-xs font-semibold text-gray-500 cursor-pointer hover:text-gray-700">
                        Ver detalle ({{ count($res['detalle']) }} mensajes)
                    </summary>
                    <ul class="mt-2 space-y-1 max-h-40 overflow-y-auto bg-gray-50 rounded-lg p-3">
                        @foreach($res['detalle'] as $linea)
                        <li class="text-xs text-gray-600">{{ $linea }}</li>
                        @endforeach
                    </ul>
                </details>
                @endif
            </div>
        </div>
        @endforeach

        <div class="flex justify-end gap-3">
            <button wire:click="reiniciar"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Nueva importación
            </button>
            <a href="{{ route('clientes.index') }}"
               class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Ver clientes
            </a>
        </div>
    </div>
    @endif

    {{-- Historial --}}
    @if(count($historial) > 0 && $paso === 'subir')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Últimas importaciones</h3>
        <div class="space-y-2">
            @foreach($historial as $h)
            <div class="flex items-center justify-between text-sm py-2 border-b border-gray-50 last:border-0">
                <div>
                    <span class="text-gray-700 font-medium">{{ $h->archivo }}</span>
                    <span class="text-xs text-gray-400 ml-2">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-green-700">✓ {{ $h->total_importadas }}</span>
                    <span class="text-yellow-600">~ {{ $h->total_duplicadas }}</span>
                    <span class="text-red-600">✗ {{ $h->total_error }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
