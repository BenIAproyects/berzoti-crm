<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text" placeholder="Buscar por cliente, RUC, N° factura, N° operación..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select wire:model.live="filtroMetodo"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los métodos</option>
                    @foreach($metodos as $m)
                    <option value="{{ $m->value }}">{{ $m->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center">
                <button wire:click="limpiarFiltros"
                        class="text-xs text-gray-500 hover:text-indigo-600 font-medium">
                    Limpiar filtros
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
        @if($pagos->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm">No se encontraron pagos.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Factura</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Monto pagado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Método</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Banco</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">N° Operación</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @foreach($pagos as $pago)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $pago->cliente_id) }}"
                               class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $pago->cliente->razon_social }}
                            </a>
                            @if($pago->cliente->ruc)
                            <p class="text-xs text-gray-400">{{ $pago->cliente->ruc }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-600">{{ $pago->factura->codigo }}</span>
                            @if($pago->factura->numero_factura)
                            <span class="block text-xs text-gray-400">{{ $pago->factura->numero_factura }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 text-base">
                            S/ {{ number_format($pago->monto_pagado, 2) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $pago->metodo_pago->label() }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $pago->banco ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs font-mono">{{ $pago->numero_operacion ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('clientes.show', $pago->cliente_id) }}"
                               class="text-xs text-gray-500 hover:text-indigo-600 font-medium">
                                Ver cliente
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totales --}}
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500">
                Total recaudado (página): <span class="font-bold text-green-600">S/ {{ number_format($pagos->sum('monto_pagado'), 2) }}</span>
            </span>
            @if($pagos->hasPages())
            <div>{{ $pagos->links() }}</div>
            @endif
        </div>
        @endif
    </div>

</div>
