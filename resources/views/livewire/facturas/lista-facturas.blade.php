<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text" placeholder="Buscar por cliente, RUC, N° factura..."
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
                    <input wire:model.live="soloVencidas" type="checkbox"
                           class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                    Solo vencidas
                </label>
                <button wire:click="limpiarFiltros"
                        class="text-xs text-gray-500 hover:text-indigo-600 font-medium whitespace-nowrap">
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
        @if($facturas->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">No se encontraron facturas.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">N° Factura</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Emisión</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Vencimiento</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Saldo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @foreach($facturas as $fac)
                    @php $vencida = $fac->estaVencida(); @endphp
                    <tr class="hover:bg-gray-50 {{ $vencida ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $fac->codigo }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $fac->cliente_id) }}"
                               class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $fac->cliente->razon_social }}
                            </a>
                            @if($fac->cliente->ruc)
                            <p class="text-xs text-gray-400">{{ $fac->cliente->ruc }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $fac->numero_factura ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $fac->fecha_emision->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 {{ $vencida ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            {{ $fac->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">
                            S/ {{ number_format($fac->total, 2) }}
                            <p class="text-xs text-gray-400 font-normal">s/IGV: S/ {{ number_format($fac->subtotal, 2) }}</p>
                        </td>
                        <td class="px-4 py-3 text-right {{ (float)$fac->saldo_pendiente > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                            S/ {{ number_format($fac->saldo_pendiente, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $fac->estado_pago->color() }}">
                                {{ $fac->estado_pago->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $fac->vendedor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if(!in_array($fac->estado_pago->value, ['pagada', 'anulada']))
                                @can('pagos.crear')
                                <button wire:click="abrirPago({{ $fac->id }}, {{ $fac->cliente_id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Registrar pago
                                </button>
                                @endcan
                                @endif
                                <a href="{{ route('clientes.show', $fac->cliente_id) }}"
                                   class="text-xs text-gray-500 hover:text-indigo-600 font-medium">
                                    Ver cliente
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($facturas->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $facturas->links() }}</div>
        @endif
        @endif
    </div>

</div>
