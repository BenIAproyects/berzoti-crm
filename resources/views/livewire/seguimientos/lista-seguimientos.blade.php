<div>
    @if($timeline->isEmpty())
    <div class="text-center py-10 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        <p class="text-sm">No hay seguimientos registrados aún.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($timeline as $item)

        {{-- ── CORREO ENVIADO ──────────────────────────────────────────── --}}
        @if($item['source'] === 'correo')
        @php $c = $item['data']; @endphp
        <div class="bg-white rounded-xl border border-indigo-100 shadow-sm p-4" wire:key="correo-{{ $c->id }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="shrink-0 w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-700">Correo enviado</p>
                            @php
                                $badge = match($c->estado_envio) {
                                    'enviado'  => 'bg-green-100 text-green-700',
                                    'fallido'  => 'bg-red-100 text-red-700',
                                    default    => 'bg-yellow-100 text-yellow-700',
                                };
                                $label = match($c->estado_envio) {
                                    'enviado'  => 'Enviado',
                                    'fallido'  => 'Fallido',
                                    default    => 'Pendiente',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ $label }}</span>
                            @if($c->abierto)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                Abierto@if($c->veces_abierto > 1) ×{{ $c->veces_abierto }}@endif
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $c->created_at->format('d/m/Y H:i') }} &middot; {{ $c->usuario->name }}</p>
                    </div>
                </div>
                @if($c->campana)
                <span class="shrink-0 text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium truncate max-w-[140px]" title="{{ $c->campana->nombre }}">
                    {{ $c->campana->nombre }}
                </span>
                @endif
            </div>

            <div class="mt-3 pl-12 space-y-1">
                <p class="text-sm font-medium text-gray-700 truncate">{{ $c->asunto }}</p>
                <p class="text-xs text-gray-400">Para: {{ $c->destinatario }}</p>
                @if($c->abierto && $c->abierto_en)
                <p class="text-xs text-emerald-600">Abierto el {{ $c->abierto_en->format('d/m/Y H:i') }}</p>
                @endif
                @if($c->estado_envio === 'fallido' && $c->error_mensaje)
                <p class="text-xs text-red-500">{{ Str::limit($c->error_mensaje, 80) }}</p>
                @endif
            </div>
        </div>

        {{-- ── SEGUIMIENTO MANUAL ──────────────────────────────────────── --}}
        @else
        @php $s = $item['data']; @endphp
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4" wire:key="seg-{{ $s->id }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center
                        {{ match($s->tipo->value) {
                            'llamada'     => 'bg-blue-100 text-blue-600',
                            'correo'      => 'bg-indigo-100 text-indigo-600',
                            'reunion'     => 'bg-purple-100 text-purple-600',
                            'whatsapp'    => 'bg-green-100 text-green-600',
                            'visita'      => 'bg-orange-100 text-orange-600',
                            'cotizacion'  => 'bg-yellow-100 text-yellow-600',
                            default       => 'bg-gray-100 text-gray-500',
                        } }}">
                        @if($s->tipo->value === 'llamada')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        @elseif($s->tipo->value === 'correo')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @elseif($s->tipo->value === 'whatsapp')
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-700">{{ $s->tipo->label() }}</p>
                        <p class="text-xs text-gray-400">{{ $s->fecha_hora->format('d/m/Y H:i') }} &middot; {{ $s->usuario->name }}</p>
                    </div>
                </div>
                @if($s->estado_comercial_nuevo)
                <span class="shrink-0 text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                    → {{ $s->estado_comercial_nuevo->label() }}
                </span>
                @endif
            </div>

            <div class="mt-3 space-y-2 pl-12">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">¿Qué se hizo?</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $s->detalle }}</p>
                </div>
                @if($s->resultado)
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">¿Qué pasó?</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $s->resultado }}</p>
                </div>
                @endif
                @if($s->proxima_accion)
                <div class="text-xs text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg">
                    <strong>Siguiente:</strong> {{ $s->proxima_accion }}
                    @if($s->fecha_proxima_accion)
                    — {{ $s->fecha_proxima_accion->format('d/m/Y') }}
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif

        @endforeach
    </div>

    @if($timeline->hasPages())
    <div class="mt-4">{{ $timeline->links() }}</div>
    @endif
    @endif
</div>
