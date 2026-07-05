<div>
    @if($cotizaciones->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm">No hay cotizaciones registradas.</p>
    </div>
    @else
    <div class="overflow-hidden rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ítems</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Monto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Campaña</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach($cotizaciones as $cot)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $cot->codigo }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $cot->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $cot->items->count() }} ítem{{ $cot->items->count() !== 1 ? 's' : '' }}
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        S/ {{ number_format($cot->monto_total, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cot->estado->color() }}">
                            {{ $cot->estado->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $cot->campana?->nombre ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('cotizaciones.imprimir', $cot->id) }}" target="_blank"
                               class="text-xs text-gray-500 hover:text-gray-700 font-medium inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Imprimir
                            </a>
                            @can('cotizaciones.editar')
                            <button wire:click="editar({{ $cot->id }})"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Editar
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @if($cot->observaciones)
                <tr class="bg-gray-50">
                    <td colspan="7" class="px-4 py-2 text-xs text-gray-500 italic">
                        {{ $cot->observaciones }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if($cotizaciones->hasPages())
    <div class="mt-3">{{ $cotizaciones->links() }}</div>
    @endif
    @endif
</div>
