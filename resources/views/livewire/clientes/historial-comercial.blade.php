<div class="space-y-5">

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Cotizaciones --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cotizaciones</span>
                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $resumen['cotizaciones_count'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">S/ {{ number_format($resumen['cotizaciones_monto'], 2) }} cotizado</p>
        </div>

        {{-- Órdenes de Compra --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Órdenes de Compra</span>
                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $resumen['ordenes_count'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">S/ {{ number_format($resumen['ordenes_monto'], 2) }} en OCs</p>
        </div>

        {{-- Facturado --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Facturado</span>
                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-amber-50">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($resumen['monto_facturado'], 0) }}</p>
            <p class="text-xs mt-0.5 {{ $resumen['saldo_pendiente'] > 0 ? 'text-red-500' : 'text-gray-400' }}">
                Saldo: S/ {{ number_format($resumen['saldo_pendiente'], 2) }}
            </p>
        </div>

        {{-- Cobrado --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cobrado</span>
                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-green-50">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-green-600">S/ {{ number_format($resumen['monto_pagado'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $resumen['porcentaje_cobrado'] }}% del total facturado</p>
        </div>
    </div>

    {{-- Filtros por tipo --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-wrap gap-2">
            @php
                $tipos = [
                    ''             => 'Todo',
                    'cotizaciones' => 'Cotizaciones',
                    'ordenes'      => 'Órdenes de Compra',
                    'facturas'     => 'Facturas',
                    'guias'        => 'Guías de Remisión',
                    'pagos'        => 'Pagos',
                    'seguimientos' => 'Seguimientos',
                ];
            @endphp
            @foreach($tipos as $valor => $etiqueta)
            <button wire:click="$set('filtroTipo', '{{ $valor }}')"
                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                        {{ $filtroTipo === $valor
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $etiqueta }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Timeline --}}
    @if($eventos->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm text-center py-12 text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm">No hay eventos registrados para este filtro.</p>
    </div>
    @else
    <div class="relative">
        {{-- Línea vertical --}}
        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

        <div class="space-y-4">
            @foreach($eventos as $evento)
            @php
                $m = $evento['modelo'];
                $fecha = $evento['fecha'];
            @endphp

            <div class="relative flex gap-4 pl-14">
                {{-- Icono en la línea --}}
                @if($evento['tipo'] === 'cotizacion')
                <div class="absolute left-2 w-6 h-6 rounded-full bg-blue-100 border-2 border-blue-400 flex items-center justify-center">
                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                @elseif($evento['tipo'] === 'orden')
                <div class="absolute left-2 w-6 h-6 rounded-full bg-indigo-100 border-2 border-indigo-400 flex items-center justify-center">
                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                @elseif($evento['tipo'] === 'factura')
                <div class="absolute left-2 w-6 h-6 rounded-full bg-amber-100 border-2 border-amber-400 flex items-center justify-center">
                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                @elseif($evento['tipo'] === 'guia')
                <div class="absolute left-2 w-6 h-6 rounded-full bg-teal-100 border-2 border-teal-400 flex items-center justify-center">
                    <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0120 8.414V15a2 2 0 01-2 2h-2"/></svg>
                </div>
                @elseif($evento['tipo'] === 'pago')
                <div class="absolute left-2 w-6 h-6 rounded-full bg-green-100 border-2 border-green-500 flex items-center justify-center">
                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @else
                <div class="absolute left-2 w-6 h-6 rounded-full bg-gray-100 border-2 border-gray-400 flex items-center justify-center">
                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                @endif

                {{-- Tarjeta --}}
                <div class="flex-1 bg-white rounded-xl border border-gray-100 shadow-sm p-4">

                    {{-- COTIZACIÓN --}}
                    @if($evento['tipo'] === 'cotizacion')
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">Cotización</span>
                                <span class="font-mono text-xs text-gray-500">{{ $m->codigo }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $m->estado->color() }}">{{ $m->estado->label() }}</span>
                                @if($m->convertida_a_oc)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">→ OC generada</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $m->items->count() }} ítem{{ $m->items->count() !== 1 ? 's' : '' }}
                                @if($m->campana) &bull; Campaña: {{ $m->campana->nombre }} @endif
                                @if($m->usuario) &bull; {{ $m->usuario->name }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-800">S/ {{ number_format($m->monto_total, 2) }}</p>
                            <p class="text-xs text-gray-400">{{ $fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- ORDEN DE COMPRA --}}
                    @elseif($evento['tipo'] === 'orden')
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide">Orden de Compra</span>
                                <span class="font-mono text-xs text-gray-500">{{ $m->codigo }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $m->estado->color() }}">{{ $m->estado->label() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                @if($m->numero_oc) N° OC cliente: {{ $m->numero_oc }} &bull; @endif
                                {{ $m->items->count() }} ítem{{ $m->items->count() !== 1 ? 's' : '' }}
                                &bull; {{ number_format($m->items->sum('cantidad_pedida')) }} unidades
                                @if($m->campana) &bull; {{ $m->campana->nombre }} @endif
                                @if($m->vendedor) &bull; {{ $m->vendedor->name }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-400">s/IGV: S/ {{ number_format($m->subtotal, 2) }}</p>
                            <p class="text-sm font-bold text-gray-800">S/ {{ number_format($m->total, 2) }}</p>
                            <p class="text-xs text-gray-400">{{ $fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- FACTURA --}}
                    @elseif($evento['tipo'] === 'factura')
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-amber-600 uppercase tracking-wide">Factura</span>
                                <span class="font-mono text-xs text-gray-500">{{ $m->codigo }}</span>
                                @if($m->numero_factura)
                                <span class="text-xs text-gray-500">{{ $m->numero_factura }}</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $m->estado_pago->color() }}">{{ $m->estado_pago->label() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                @if($m->fecha_vencimiento) Vence: {{ $m->fecha_vencimiento->format('d/m/Y') }} &bull; @endif
                                @if($m->ordenCompra) OC: {{ $m->ordenCompra->codigo }} &bull; @endif
                                @if($m->vendedor) {{ $m->vendedor->name }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-800">S/ {{ number_format($m->total, 2) }}</p>
                            @if((float)$m->saldo_pendiente > 0)
                            <p class="text-xs text-red-500">Saldo: S/ {{ number_format($m->saldo_pendiente, 2) }}</p>
                            @else
                            <p class="text-xs text-green-500">Pagada completa</p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- GUÍA DE REMISIÓN --}}
                    @elseif($evento['tipo'] === 'guia')
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-teal-600 uppercase tracking-wide">Guía de Remisión</span>
                                <span class="font-mono text-xs text-gray-500">{{ $m->codigo }}</span>
                                @if($m->numero_guia)
                                <span class="text-xs text-gray-500">{{ $m->numero_guia }}</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $m->estado_entrega->color() }}">{{ $m->estado_entrega->label() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $m->items->count() }} ítem{{ $m->items->count() !== 1 ? 's' : '' }}
                                &bull; {{ number_format($m->items->sum('cantidad_enviada')) }} unidades
                                @if($m->fecha_entrega) &bull; Entrega: {{ $m->fecha_entrega->format('d/m/Y') }} @endif
                                @if($m->vendedor) &bull; {{ $m->vendedor->name }} @endif
                            </p>
                            @if($m->direccion_entrega)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $m->direccion_entrega }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-400">{{ $fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- PAGO --}}
                    @elseif($evento['tipo'] === 'pago')
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-green-600 uppercase tracking-wide">Pago recibido</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $m->metodo_pago->label() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                Factura: {{ $m->factura->codigo }}
                                @if($m->factura->numero_factura) ({{ $m->factura->numero_factura }}) @endif
                                @if($m->banco) &bull; {{ $m->banco }} @endif
                                @if($m->numero_operacion) &bull; N° op: {{ $m->numero_operacion }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-lg font-bold text-green-600">S/ {{ number_format($m->monto_pagado, 2) }}</p>
                            <p class="text-xs text-gray-400">{{ $fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- SEGUIMIENTO --}}
                    @else
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Seguimiento</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $m->tipo->label() }}</span>
                            </div>
                            @if($m->detalle)
                            <p class="text-sm text-gray-700">{{ $m->detalle }}</p>
                            @endif
                            @if($m->resultado)
                            <p class="text-xs text-gray-500 mt-0.5">Resultado: {{ $m->resultado }}</p>
                            @endif
                            @if($m->usuario)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $m->usuario->name }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-400">{{ $fecha->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
