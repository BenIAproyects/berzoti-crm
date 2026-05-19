<div>
    {{-- Barra de filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap gap-3 items-end">

            <div class="flex-1 min-w-[200px]">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text"
                       placeholder="Buscar por nombre, RUC, correo, teléfono..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="filtroTipo"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                    @endforeach
                </select>
            </div>

            @can('clientes.asignar')
            <div>
                <select wire:model.live="filtroVendedor"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los vendedores</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            @endcan

            @if($busqueda || $filtroEstado || $filtroTipo || $filtroVendedor)
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
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Empresa</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Próximo contacto</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
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
                        <x-estado-badge :estado="$cliente->estado_comercial" />
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
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">
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
