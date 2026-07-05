<div>
    @if($facturas->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm">No hay facturas registradas.</p>
    </div>
    @else
    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">N° Factura</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Emisión</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vencimiento</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Pagado</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Saldo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">OC</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach($facturas as $fac)
                @php $vencida = $fac->estaVencida(); @endphp
                <tr class="hover:bg-gray-50 {{ $vencida ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $fac->codigo }}</td>
                    <td class="px-4 py-3 text-gray-700 font-medium">{{ $fac->numero_factura ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $fac->fecha_emision->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 {{ $vencida ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        {{ $fac->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                        @if($vencida)
                        <span class="block text-xs font-normal text-red-500">Vencida</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($fac->total, 2) }}</td>
                    <td class="px-4 py-3 text-right text-green-600">S/ {{ number_format($fac->monto_pagado, 2) }}</td>
                    <td class="px-4 py-3 text-right {{ (float)$fac->saldo_pendiente > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                        S/ {{ number_format($fac->saldo_pendiente, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $fac->estado_pago->color() }}">
                            {{ $fac->estado_pago->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $fac->ordenCompra?->codigo ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('facturas.editar')
                        <button wire:click="$dispatch('editar-factura', { id: {{ $fac->id }} })"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Editar
                        </button>
                        @endcan
                    </td>
                </tr>
                @if($fac->observaciones)
                <tr class="bg-gray-50">
                    <td colspan="10" class="px-4 py-2 text-xs text-gray-500 italic">{{ $fac->observaciones }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if($facturas->hasPages())
    <div class="mt-3">{{ $facturas->links() }}</div>
    @endif
    @endif
</div>
