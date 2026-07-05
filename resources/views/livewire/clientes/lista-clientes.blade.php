<div>
    {{-- Barra de filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 space-y-3">
        {{-- Fila 1 --}}
        <div class="flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-[200px]">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text"
                       placeholder="Buscar por nombre, RUC, correo, teléfono..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <select wire:model.live="filtroEstado"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los estados</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="filtroTipo"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los tipos</option>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                @endforeach
            </select>

            @can('clientes.asignar')
            <select wire:model.live="filtroVendedor"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los vendedores</option>
                @foreach($vendedores as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>
            @endcan
        </div>

        {{-- Fila 2 --}}
        <div class="flex flex-wrap gap-3 items-center">
            <select wire:model.live="filtroFuente"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas las fuentes</option>
                @foreach($fuentes as $f)
                    <option value="{{ $f->value }}">{{ $f->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="filtroSegmento"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los segmentos</option>
                @foreach($segmentos as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </select>

            @if($zonas->isNotEmpty())
            <select wire:model.live="filtroZona"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas las zonas</option>
                @foreach($zonas as $z)
                    <option value="{{ $z }}">{{ $z }}</option>
                @endforeach
            </select>
            @endif

            @if($busqueda || $filtroEstado || $filtroTipo || $filtroVendedor || $filtroFuente || $filtroSegmento || $filtroZona)
            <button wire:click="limpiarFiltros"
                    class="text-sm text-gray-500 hover:text-red-600 underline">
                Limpiar filtros
            </button>
            @endif

            @can('clientes.exportar')
            <a href="{{ route('clientes.exportar', array_filter([
                    'busqueda'       => $busqueda,
                    'filtroEstado'   => $filtroEstado,
                    'filtroTipo'     => $filtroTipo,
                    'filtroVendedor' => $filtroVendedor,
                ])) }}"
               class="ml-auto flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Excel
            </a>
            @endcan
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            @php
            $th = fn(string $campo, string $label) =>
                '<button wire:click="ordenar(\'' . $campo . '\')" class="group inline-flex items-center gap-1 font-semibold text-gray-600 hover:text-indigo-600 whitespace-nowrap">'
                . $label
                . '<span class="text-xs ' . ($ordenarPor === $campo ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400') . '">'
                . ($ordenarPor === $campo ? ($ordenarDir === 'asc' ? '▲' : '▼') : '⇅')
                . '</span></button>';
            @endphp
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">{!! $th('razon_social', 'Empresa') !!}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th class="px-4 py-3 text-left">{!! $th('tipo_cliente', 'Tipo') !!}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fuente / Segmento</th>
                    <th class="px-4 py-3 text-left">{!! $th('estado_comercial', 'Estado') !!}</th>
                    <th class="px-4 py-3 text-right">{!! $th('cantidad_compra', 'Cant. usual') !!}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                    <th class="px-4 py-3 text-left">{!! $th('fecha_proximo_contacto', 'Próximo contacto') !!}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                $estadoColorMap = [
                    'gray'   => 'bg-gray-100 text-gray-700',
                    'yellow' => 'bg-yellow-100 text-yellow-700',
                    'blue'   => 'bg-blue-100 text-blue-700',
                    'indigo' => 'bg-indigo-100 text-indigo-700',
                    'purple' => 'bg-purple-100 text-purple-700',
                    'orange' => 'bg-orange-100 text-orange-700',
                    'amber'  => 'bg-amber-100 text-amber-700',
                    'green'  => 'bg-green-100 text-green-700',
                    'red'    => 'bg-red-100 text-red-700',
                    'slate'  => 'bg-slate-100 text-slate-700',
                ];
                @endphp
                @forelse($clientes as $cliente)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $cliente) }}"
                           class="font-medium text-indigo-700 hover:text-indigo-900">
                            {{ $cliente->razon_social }}
                        </a>
                        @if($cliente->ruc)
                            <p class="text-xs text-gray-400">RUC: {{ $cliente->ruc }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-800">{{ $cliente->contacto_principal }}</p>
                        @if($cliente->correo)
                            <p class="text-xs text-gray-400">{{ $cliente->correo }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-600">{{ $cliente->tipo_cliente->label() }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            @if($cliente->fuente)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $cliente->fuente->value === 'lima' ? 'bg-blue-100 text-blue-700' : ($cliente->fuente->value === 'provincia' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $cliente->fuente->label() }}
                                </span>
                            @endif
                            @if($cliente->segmento)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $cliente->segmento->value === 'vip' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $cliente->segmento->label() }}
                                </span>
                            @endif
                            @if(!$cliente->fuente && !$cliente->segmento)
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @can('clientes.editar')
                        @php $clrClass = $estadoColorMap[$cliente->estado_comercial->color()] ?? 'bg-gray-100 text-gray-700'; @endphp
                        <select wire:change="cambiarEstado({{ $cliente->id }}, $event.target.value)"
                                class="text-xs font-medium rounded-full px-2.5 py-1 border-0 cursor-pointer {{ $clrClass }} focus:ring-1 focus:ring-indigo-400 focus:outline-none">
                            @foreach($estados as $e)
                            <option value="{{ $e->value }}" @selected($e === $cliente->estado_comercial)>{{ $e->label() }}</option>
                            @endforeach
                        </select>
                        @else
                        <x-estado-badge :estado="$cliente->estado_comercial" />
                        @endcan
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($cliente->cantidad_compra)
                        <span class="font-semibold text-gray-800">{{ number_format($cliente->cantidad_compra) }}</span>
                        <span class="text-xs text-gray-400 ml-0.5">und</span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $cliente->vendedor?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $cliente->fecha_proximo_contacto?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('clientes.show', $cliente) }}"
                               class="text-gray-400 hover:text-indigo-600" title="Ver ficha">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @can('clientes.editar')
                            <a href="{{ route('clientes.edit', $cliente) }}"
                               class="text-gray-400 hover:text-amber-600" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center text-gray-400">
                        No se encontraron clientes con los filtros aplicados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($clientes->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $clientes->links() }}
        </div>
        @endif
    </div>

    <div class="mt-2 text-xs text-gray-400">
        {{ $clientes->total() }} cliente(s) encontrado(s)
    </div>
</div>
