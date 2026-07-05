<div>
    @if($pagos->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="text-sm">No hay pagos registrados.</p>
    </div>
    @else
    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Factura</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Monto pagado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Método</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Banco / N° Op.</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Obs.</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach($pagos as $pago)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 font-medium">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-gray-600">{{ $pago->factura->codigo }}</span>
                        @if($pago->factura->numero_factura)
                        <span class="block text-xs text-gray-500">{{ $pago->factura->numero_factura }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-green-600">
                        S/ {{ number_format($pago->monto_pagado, 2) }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $pago->metodo_pago->label() }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($pago->banco)
                        <span class="block font-medium text-gray-600">{{ $pago->banco }}</span>
                        @endif
                        @if($pago->numero_operacion)
                        <span class="font-mono">{{ $pago->numero_operacion }}</span>
                        @endif
                        @if(!$pago->banco && !$pago->numero_operacion) — @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 max-w-[180px] truncate">
                        {{ $pago->observaciones ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            @can('pagos.editar')
                            <button wire:click="$dispatch('editar-pago', { id: {{ $pago->id }} })"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Editar
                            </button>
                            @endcan
                            @can('pagos.eliminar')
                            <button wire:click="eliminar({{ $pago->id }})"
                                    wire:confirm="¿Eliminar este pago? La factura se recalculará automáticamente."
                                    class="text-xs text-red-500 hover:text-red-700 font-medium">
                                Eliminar
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($pagos->hasPages())
    <div class="mt-3">{{ $pagos->links() }}</div>
    @endif
    @endif
</div>
