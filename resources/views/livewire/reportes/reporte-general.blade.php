<div>
    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 mb-4 bg-gray-100 p-1 rounded-lg w-fit">
        @foreach([
            ['tab' => 'clientes',     'label' => 'Clientes'],
            ['tab' => 'seguimientos', 'label' => 'Seguimientos'],
            ['tab' => 'tareas',       'label' => 'Tareas'],
            ['tab' => 'cotizaciones', 'label' => 'Cotizaciones'],
            ['tab' => 'correos',      'label' => 'Correos'],
            ['tab' => 'sin_contacto', 'label' => 'Sin contacto'],
        ] as $t)
        <button wire:click="$set('tab', '{{ $t['tab'] }}')"
                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                       {{ $tab === $t['tab'] ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            @if(in_array($tab, ['clientes', 'sin_contacto']))
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado comercial</label>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\EstadoComercial::cases() as $e)
                    <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($tab === 'tareas')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="completada">Completadas</option>
                </select>
            </div>
            @endif

            @if($tab === 'cotizaciones')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\EstadoCotizacion::cases() as $e)
                    <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('supervisor'))
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Vendedor</label>
                <select wire:model.live="filtroUsuario"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    @foreach($vendedores as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($tab === 'sin_contacto')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sin contacto hace más de</label>
                <div class="flex items-center gap-2">
                    <input wire:model.live="diasSinContacto" type="number" min="1" max="365"
                           class="w-20 rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="text-sm text-gray-500">días</span>
                </div>
            </div>
            @endif

            <div class="ml-auto">
                <a href="{{ route('reportes.exportar', ['tab' => $tab, 'estado' => $filtroEstado, 'usuario' => $filtroUsuario]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Exportar CSV
                </a>
            </div>
        </div>
    </div>

    {{-- Tabla dinámica --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @if(in_array($tab, ['clientes', 'sin_contacto']))
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Empresa</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Último contacto</th>
                    @elseif($tab === 'seguimientos')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Detalle</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Usuario</th>
                    @elseif($tab === 'tareas')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tarea</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vencimiento</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Usuario</th>
                    @elseif($tab === 'cotizaciones')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Monto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    @elseif($tab === 'correos')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Destinatario</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($datos as $fila)
                <tr class="hover:bg-gray-50">
                    @if(in_array($tab, ['clientes', 'sin_contacto']))
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $fila) }}" class="font-medium text-indigo-700 hover:underline">{{ $fila->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $fila->contacto_principal }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $fila->estado_comercial->color() }}">{{ $fila->estado_comercial->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->vendedor?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs {{ !$fila->fecha_ultimo_contacto ? 'text-red-500' : 'text-gray-500' }}">
                        {{ $fila->fecha_ultimo_contacto?->format('d/m/Y') ?? 'Nunca' }}
                    </td>
                    @elseif($tab === 'seguimientos')
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->fecha_hora->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $fila->cliente_id) }}" class="text-indigo-700 hover:underline font-medium">{{ $fila->cliente->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $fila->tipo->label() }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $fila->detalle }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->usuario->name }}</td>
                    @elseif($tab === 'tareas')
                    @php $vencida = $fila->estaVencida(); @endphp
                    <td class="px-4 py-3 {{ $vencida ? 'text-red-700 font-medium' : 'text-gray-800' }}">{{ $fila->titulo }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $fila->cliente_id) }}" class="text-indigo-700 hover:underline text-xs">{{ $fila->cliente->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-xs {{ $vencida ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $fila->fecha_vencimiento->format('d/m/Y') }}
                        @if($vencida) <span class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">Vencida</span> @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $fila->estado === 'completada' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($fila->estado) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->usuario->name }}</td>
                    @elseif($tab === 'cotizaciones')
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $fila->codigo }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $fila->cliente_id) }}" class="text-indigo-700 hover:underline font-medium">{{ $fila->cliente->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">S/ {{ number_format($fila->monto_total, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $fila->estado->color() }}">{{ $fila->estado->label() }}</span>
                    </td>
                    @elseif($tab === 'correos')
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $fila->destinatario }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $fila->asunto }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $fila->estado_envio === 'enviado' ? 'bg-green-100 text-green-700' : ($fila->estado_envio === 'fallido' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($fila->estado_envio) }}
                        </span>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay datos para este reporte.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($datos, 'hasPages') && $datos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $datos->links() }}</div>
        @endif
    </div>
</div>
