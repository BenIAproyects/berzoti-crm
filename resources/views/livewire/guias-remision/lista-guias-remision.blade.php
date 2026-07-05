<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text" placeholder="Buscar por cliente, RUC, código, N° guía..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select wire:model.live="filtroEstado"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $e)
                    <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filtroVendedor"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los vendedores</option>
                    @foreach($vendedores as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                    <input wire:model.live="soloPendientes" type="checkbox"
                           class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500">
                    Pendientes de entrega
                </label>
                <button wire:click="limpiarFiltros"
                        class="text-xs text-gray-500 hover:text-indigo-600 font-medium">
                    Limpiar
                </button>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 max-w-xs">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Desde</label>
                <input wire:model.live="fechaDesde" type="date"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                <input wire:model.live="fechaHasta" type="date"
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    {{-- Lista --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($guias->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0120 8.414V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
            </svg>
            <p class="text-sm">No se encontraron guías de remisión.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">N° Guía</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Emisión</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Entrega</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cant. total</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">OC / Factura</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @foreach($guias as $guia)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $guia->codigo }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $guia->cliente_id) }}"
                               class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $guia->cliente->razon_social }}
                            </a>
                            @if($guia->cliente->ruc)
                            <p class="text-xs text-gray-400">{{ $guia->cliente->ruc }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $guia->numero_guia ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $guia->fecha_emision->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $guia->fecha_entrega?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ number_format($guia->items->sum('cantidad_enviada')) }} unid.
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $guia->estado_entrega->color() }}">
                                {{ $guia->estado_entrega->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($guia->ordenCompra)
                            <span class="block">{{ $guia->ordenCompra->codigo }}</span>
                            @endif
                            @if($guia->factura)
                            <span class="block text-gray-400">{{ $guia->factura->codigo }}</span>
                            @endif
                            @if(!$guia->ordenCompra && !$guia->factura) — @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $guia->vendedor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('clientes.show', $guia->cliente_id) }}"
                               class="text-xs text-gray-500 hover:text-indigo-600 font-medium">
                                Ver cliente
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($guias->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $guias->links() }}</div>
        @endif
        @endif
    </div>

</div>
