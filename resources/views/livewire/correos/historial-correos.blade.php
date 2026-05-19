<div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex items-center gap-3 flex-wrap">
            <select wire:model.live="filtroEstado"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="enviado">Enviado</option>
                <option value="fallido">Fallido</option>
            </select>

            @livewire('correos.sincronizar-aperturas')

            @php $hayPendientes = \App\Models\CorreoEnviado::where('estado_envio','pendiente')->when($campanaId ?? null, fn($q) => $q->where('campana_id', $campanaId))->exists(); @endphp
            @if($hayPendientes)
            <button wire:click="enviarPendientes"
                    wire:loading.attr="disabled"
                    wire:target="enviarPendientes"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                <svg wire:loading wire:target="enviarPendientes" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                <svg wire:loading.remove wire:target="enviarPendientes" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Enviar pendientes
            </button>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Destinatario</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Campaña</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Enviado por</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Apertura</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($correos as $correo)
                <tr wire:key="correo-{{ $correo->id }}" class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $correo->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-800 font-medium">{{ $correo->destinatario }}</p>
                        @if($correo->cliente)
                        <p class="text-xs text-gray-400">{{ $correo->cliente->razon_social }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700 max-w-xs truncate">{{ $correo->asunto }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $correo->campana?->nombre ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $correo->usuario->name }}</td>
                    <td class="px-4 py-3">
                        @php
                        $badge = match($correo->estado_envio) {
                            'enviado'  => 'bg-green-100 text-green-700',
                            'fallido'  => 'bg-red-100 text-red-700',
                            default    => 'bg-yellow-100 text-yellow-700',
                        };
                        $label = match($correo->estado_envio) {
                            'enviado'  => 'Enviado',
                            'fallido'  => 'Fallido',
                            default    => 'Pendiente',
                        };
                        @endphp
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ $label }}
                            </span>
                            @if($correo->estado_envio === 'fallido')
                            <button wire:click="reintentar({{ $correo->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="reintentar({{ $correo->id }})"
                                    class="text-xs text-red-600 hover:text-red-800 underline disabled:opacity-50">
                                Reintentar
                            </button>
                            @endif
                        </div>
                        @if($correo->estado_envio === 'fallido' && $correo->error_mensaje)
                        <p class="text-xs text-red-500 mt-0.5 max-w-xs truncate" title="{{ $correo->error_mensaje }}">
                            {{ Str::limit($correo->error_mensaje, 60) }}
                        </p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($correo->abierto)
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                Abierto
                            </span>
                            @if($correo->veces_abierto > 1)
                            <span class="text-xs text-gray-400">×{{ $correo->veces_abierto }}</span>
                            @endif
                        </div>
                        @if($correo->abierto_en)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $correo->abierto_en->format('d/m/Y H:i') }}</p>
                        @endif
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                        No hay correos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($correos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $correos->links() }}</div>
        @endif
    </div>
</div>
