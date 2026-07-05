<div>
    @if($ordenes->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-sm">No hay órdenes de compra registradas.</p>
    </div>
    @else
    <div class="overflow-hidden rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">N° OC</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha OC</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ítems</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Subtotal</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">IGV</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach($ordenes as $oc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $oc->codigo }}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $oc->numero_oc ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $oc->fecha_oc->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $oc->items->count() }} ítem{{ $oc->items->count() !== 1 ? 's' : '' }}
                        <span class="block text-gray-400">
                            {{ number_format($oc->items->sum('cantidad_pedida')) }} unid.
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">S/ {{ number_format($oc->subtotal, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-500 text-xs">S/ {{ number_format($oc->igv, 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($oc->total, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $oc->estado->color() }}">
                            {{ $oc->estado->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('ordenes.editar')
                        <button wire:click="$dispatch('editar-orden-compra', { id: {{ $oc->id }} })"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Editar
                        </button>
                        @endcan
                    </td>
                </tr>
                @if($oc->observaciones)
                <tr class="bg-gray-50">
                    <td colspan="9" class="px-4 py-2 text-xs text-gray-500 italic">{{ $oc->observaciones }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if($ordenes->hasPages())
    <div class="mt-3">{{ $ordenes->links() }}</div>
    @endif
    @endif
</div>
