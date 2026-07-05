<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text" placeholder="Buscar por cliente, RUC, código OC..."
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
            <div class="flex items-center gap-2">
                <button wire:click="limpiarFiltros"
                        class="text-xs text-gray-500 hover:text-indigo-600 font-medium whitespace-nowrap">
                    Limpiar filtros
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3 max-w-sm">
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
        @if($ordenes->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No se encontraron órdenes de compra.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">N° OC</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha OC</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Campaña</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @foreach($ordenes as $oc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $oc->codigo }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $oc->cliente_id) }}"
                               class="font-medium text-indigo-600 hover:text-indigo-800 text-sm">
                                {{ $oc->cliente->razon_social }}
                            </a>
                            @if($oc->cliente->ruc)
                            <p class="text-xs text-gray-400">{{ $oc->cliente->ruc }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $oc->numero_oc ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $oc->fecha_oc->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">
                            S/ {{ number_format($oc->total, 2) }}
                            <p class="text-xs text-gray-400 font-normal">s/IGV: S/ {{ number_format($oc->subtotal, 2) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $oc->estado->color() }}">
                                {{ $oc->estado->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $oc->vendedor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $oc->campana?->nombre ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('clientes.show', $oc->cliente_id) }}"
                               class="text-xs text-gray-500 hover:text-indigo-600 font-medium">
                                Ver cliente
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($ordenes->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $ordenes->links() }}</div>
        @endif
        @endif
    </div>

</div>
