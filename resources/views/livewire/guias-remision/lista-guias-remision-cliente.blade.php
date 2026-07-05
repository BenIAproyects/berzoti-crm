<div>
    @if($guias->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0120 8.414V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
        </svg>
        <p class="text-sm">No hay guías de remisión registradas.</p>
    </div>
    @else
    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">N° Guía</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Emisión</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Entrega</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ítems / Cant.</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">OC / Factura</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach($guias as $guia)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $guia->codigo }}</td>
                    <td class="px-4 py-3 text-gray-700 font-medium">{{ $guia->numero_guia ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $guia->fecha_emision->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $guia->fecha_entrega?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $guia->items->count() }} ítem{{ $guia->items->count() !== 1 ? 's' : '' }}
                        <span class="block text-gray-400">
                            {{ number_format($guia->items->sum('cantidad_enviada')) }} unid.
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $guia->estado_entrega->color() }}">
                            {{ $guia->estado_entrega->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($guia->ordenCompra)
                        <span class="block">OC: {{ $guia->ordenCompra->codigo }}</span>
                        @endif
                        @if($guia->factura)
                        <span class="block">FAC: {{ $guia->factura->codigo }}</span>
                        @endif
                        @if(!$guia->ordenCompra && !$guia->factura) — @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('guias.editar')
                        <button wire:click="$dispatch('editar-guia-remision', { id: {{ $guia->id }} })"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Editar
                        </button>
                        @endcan
                    </td>
                </tr>
                @if($guia->observaciones || $guia->direccion_entrega)
                <tr class="bg-gray-50">
                    <td colspan="8" class="px-4 py-2 text-xs text-gray-500">
                        @if($guia->direccion_entrega)
                        <span class="font-medium text-gray-600">Entrega:</span> {{ $guia->direccion_entrega }}
                        @endif
                        @if($guia->observaciones)
                        <span class="ml-4 italic">{{ $guia->observaciones }}</span>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if($guias->hasPages())
    <div class="mt-3">{{ $guias->links() }}</div>
    @endif
    @endif
</div>
