<div>
    {{-- ===== Tabs ===== --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-4 overflow-x-auto">
        <div class="flex min-w-max border-b border-gray-200">
            {{-- Commercial --}}
            @foreach([
                ['key' => 'ventas',     'label' => 'Ventas'],
                ['key' => 'ordenes',    'label' => 'OCs Pendientes'],
                ['key' => 'cobranzas',  'label' => 'Cobranzas'],
                ['key' => 'guias',      'label' => 'Guías Pendientes'],
                ['key' => 'conversion', 'label' => 'Conversión'],
            ] as $t)
            <button wire:click="$set('tab', '{{ $t['key'] }}')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                           {{ $tab === $t['key'] ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $t['label'] }}
            </button>
            @endforeach

            <div class="w-px bg-gray-200 mx-1 my-2"></div>

            {{-- Legacy --}}
            @foreach([
                ['key' => 'clientes',     'label' => 'Clientes'],
                ['key' => 'seguimientos', 'label' => 'Seguimientos'],
                ['key' => 'tareas',       'label' => 'Tareas'],
                ['key' => 'cotizaciones', 'label' => 'Cotizaciones'],
                ['key' => 'correos',      'label' => 'Correos'],
                ['key' => 'sin_contacto', 'label' => 'Sin contacto'],
            ] as $t)
            <button wire:click="$set('tab', '{{ $t['key'] }}')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                           {{ $tab === $t['key'] ? 'border-gray-600 text-gray-800' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $t['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ===== Filters ===== --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">

            {{-- Date range (commercial tabs) --}}
            @if(!in_array($tab, ['clientes', 'seguimientos', 'tareas', 'cotizaciones', 'correos', 'sin_contacto']))
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
                <input type="date" wire:model.live="fechaDesde"
                       class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
                <input type="date" wire:model.live="fechaHasta"
                       class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            @endif

            {{-- Estado (legacy tabs) --}}
            @if(in_array($tab, ['clientes', 'sin_contacto']))
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado comercial</label>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\EstadoComercial::cases() as $e)
                    <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            @elseif($tab === 'tareas')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="completada">Completadas</option>
                </select>
            </div>
            @elseif($tab === 'cotizaciones')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\EstadoCotizacion::cases() as $e)
                    <option value="{{ $e->value }}">{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Agrupar por (ventas tab only) --}}
            @if($tab === 'ventas')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Agrupar por</label>
                <select wire:model.live="agruparPor"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="cliente">Cliente</option>
                    <option value="vendedor">Vendedor</option>
                    <option value="zona">Zona</option>
                    <option value="segmento">Segmento</option>
                    <option value="campana">Campaña</option>
                </select>
            </div>
            @endif

            {{-- Días sin contacto --}}
            @if($tab === 'sin_contacto')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sin contacto hace más de</label>
                <div class="flex items-center gap-2">
                    <input wire:model.live="diasSinContacto" type="number" min="1" max="365"
                           class="w-20 rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="text-sm text-gray-500">días</span>
                </div>
            </div>
            @endif

            {{-- Vendedor --}}
            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('supervisor'))
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Vendedor</label>
                <select wire:model.live="filtroUsuario"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos</option>
                    @foreach($vendedores as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Export --}}
            <div class="ml-auto flex gap-2">
                @php
                    $exportTab = match($tab) {
                        'ordenes'   => 'ordenes',
                        'cobranzas' => 'cobranzas',
                        'guias'     => 'guias',
                        default     => $tab,
                    };
                    $exportParams = array_filter([
                        'tab'      => $exportTab,
                        'estado'   => $filtroEstado,
                        'usuario'  => $filtroUsuario,
                        'desde'    => $fechaDesde,
                        'hasta'    => $fechaHasta,
                    ]);
                @endphp
                <a href="{{ route('reportes.exportar', $exportParams) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar CSV
                </a>
            </div>
        </div>
    </div>

    {{-- ===== KPI Cards (commercial tabs) ===== --}}
    @if(!in_array($tab, ['clientes', 'seguimientos', 'tareas', 'cotizaciones', 'correos', 'sin_contacto']))
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Órdenes de Compra</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $kpis['ordenes_count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">S/ {{ number_format($kpis['ordenes_total'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Facturado</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">S/ {{ number_format($kpis['facturado'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Cobrado</p>
            <p class="text-2xl font-bold text-green-700 mt-1">S/ {{ number_format($kpis['cobrado'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Por Cobrar</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">S/ {{ number_format($kpis['pendiente'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Vencido</p>
            <p class="text-2xl font-bold text-red-600 mt-1">S/ {{ number_format($kpis['vencido'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">% Cobrado</p>
            @php $pct = $kpis['facturado'] > 0 ? round(($kpis['cobrado'] / $kpis['facturado']) * 100) : 0; @endphp
            <p class="text-2xl font-bold {{ $pct >= 80 ? 'text-green-700' : ($pct >= 50 ? 'text-amber-600' : 'text-red-600') }} mt-1">{{ $pct }}%</p>
            <div class="mt-2 bg-gray-100 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== Tab Content ===== --}}

    {{-- VENTAS --}}
    @if($tab === 'ventas')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @if($agruparPor === 'cliente')
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">OCs</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Total OC</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Facturado</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Cobrado</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Pendiente</th>
                    @elseif($agruparPor === 'vendedor')
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">OCs</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Total OC</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Facturado</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Cobrado</th>
                    @else
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ ucfirst($agruparPor === 'campana' ? 'Campaña' : $agruparPor) }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Clientes</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">OCs</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Total OC</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($datosVentas as $fila)
                <tr class="hover:bg-gray-50">
                    @if($agruparPor === 'cliente')
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $fila->cliente_id) }}" class="font-medium text-indigo-700 hover:underline">{{ $fila->cliente?->razon_social ?? '—' }}</a>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $fila->ocs }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($fila->total_oc, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">S/ {{ number_format($fila->facturado, 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700 font-medium">S/ {{ number_format($fila->cobrado, 2) }}</td>
                        <td class="px-4 py-3 text-right {{ $fila->pendiente > 0 ? 'text-amber-600 font-medium' : 'text-gray-400' }}">S/ {{ number_format($fila->pendiente, 2) }}</td>
                    @elseif($agruparPor === 'vendedor')
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $fila->vendedor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $fila->ocs }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($fila->total_oc, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">S/ {{ number_format($fila->facturado, 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700 font-medium">S/ {{ number_format($fila->cobrado, 2) }}</td>
                    @elseif($agruparPor === 'campana')
                        <td class="px-4 py-3 text-gray-800">{{ $fila->campana?->nombre ?? '(Sin campaña)' }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $fila->clientes_count }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $fila->ocs }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($fila->total_oc, 2) }}</td>
                    @else
                        <td class="px-4 py-3 text-gray-800">{{ $fila->grupo ?? '(Sin definir)' }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $fila->clientes_count }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $fila->ocs }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($fila->total_oc, 2) }}</td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Sin datos para el período seleccionado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- ÓRDENES ABIERTAS --}}
    @if($tab === 'ordenes')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">N° OC</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ordenesAbiertas as $oc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $oc->codigo }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $oc->cliente_id) }}" class="font-medium text-indigo-700 hover:underline">{{ $oc->cliente?->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $oc->numero_oc ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $oc->fecha_oc->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $oc->estado->color() }}">{{ $oc->estado->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($oc->total, 2) }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $oc->vendedor?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No hay órdenes abiertas.</td></tr>
                @endforelse
            </tbody>
            @if($ordenesAbiertas->count())
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="5" class="px-4 py-2 text-xs font-semibold text-gray-500">Total ({{ $ordenesAbiertas->count() }} órdenes)</td>
                    <td class="px-4 py-2 text-right font-bold text-gray-800">S/ {{ number_format($ordenesAbiertas->sum('total'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @endif

    {{-- COBRANZAS --}}
    @if($tab === 'cobranzas')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Factura</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Emisión</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vencimiento</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Cobrado</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Saldo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($cobranzas as $fac)
                @php $vencida = $fac->estaVencida(); @endphp
                <tr class="hover:bg-gray-50 {{ $vencida ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $fac->codigo }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $fac->cliente_id) }}" class="font-medium text-indigo-700 hover:underline">{{ $fac->cliente?->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fac->fecha_emision->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-xs {{ $vencida ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $fac->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                        @if($vencida)
                            @php $dias = now()->diffInDays($fac->fecha_vencimiento); @endphp
                            <span class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">+{{ $dias }}d</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($fac->total, 2) }}</td>
                    <td class="px-4 py-3 text-right text-green-700">S/ {{ number_format($fac->monto_pagado, 2) }}</td>
                    <td class="px-4 py-3 text-right font-bold {{ $vencida ? 'text-red-600' : 'text-amber-600' }}">S/ {{ number_format($fac->saldo_pendiente, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $fac->estado_pago->color() }}">{{ $fac->estado_pago->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fac->vendedor?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-10 text-center text-gray-400">No hay cobranzas pendientes.</td></tr>
                @endforelse
            </tbody>
            @if($cobranzas->count())
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="4" class="px-4 py-2 text-xs font-semibold text-gray-500">Total ({{ $cobranzas->count() }} facturas)</td>
                    <td class="px-4 py-2 text-right font-bold text-gray-800">S/ {{ number_format($cobranzas->sum('total'), 2) }}</td>
                    <td class="px-4 py-2 text-right font-bold text-green-700">S/ {{ number_format($cobranzas->sum('monto_pagado'), 2) }}</td>
                    <td class="px-4 py-2 text-right font-bold text-amber-600">S/ {{ number_format($cobranzas->sum('saldo_pendiente'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @endif

    {{-- GUÍAS PENDIENTES --}}
    @if($tab === 'guias')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">N° Guía</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">OC</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">F. Emisión</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">F. Entrega</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Dirección</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($guiasPendientes as $guia)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $guia->codigo }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $guia->cliente_id) }}" class="font-medium text-indigo-700 hover:underline">{{ $guia->cliente?->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $guia->numero_guia ?: '—' }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $guia->ordenCompra?->codigo ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $guia->fecha_emision->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-xs {{ !$guia->fecha_entrega ? 'text-gray-400' : 'text-gray-600' }}">{{ $guia->fecha_entrega?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $guia->estado_entrega->color() }}">{{ $guia->estado_entrega->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate text-xs">{{ $guia->direccion_entrega }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $guia->vendedor?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-10 text-center text-gray-400">No hay guías pendientes de entrega.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- CONVERSIÓN --}}
    @if($tab === 'conversion')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Cotización → OC --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Cotización → Orden de Compra</h3>
            <div class="flex items-end gap-6 mb-4">
                <div>
                    <p class="text-xs text-gray-500">Cotizaciones</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $conversion['cotizaciones_count'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400">S/ {{ number_format($conversion['cotizaciones_monto'] ?? 0, 2) }}</p>
                </div>
                <div class="text-gray-300 text-2xl">→</div>
                <div>
                    <p class="text-xs text-gray-500">Convertidas a OC</p>
                    <p class="text-3xl font-bold text-indigo-700">{{ $conversion['convertidas_oc'] ?? 0 }}</p>
                </div>
            </div>
            @php $tasa1 = $conversion['tasa_cot_oc'] ?? 0; @endphp
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $tasa1 >= 50 ? 'bg-green-500' : ($tasa1 >= 25 ? 'bg-amber-400' : 'bg-red-400') }}"
                         style="width: {{ min($tasa1, 100) }}%"></div>
                </div>
                <span class="text-xl font-bold {{ $tasa1 >= 50 ? 'text-green-700' : ($tasa1 >= 25 ? 'text-amber-600' : 'text-red-600') }}">{{ $tasa1 }}%</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Tasa de conversión cotización → OC</p>
        </div>

        {{-- OC → Factura --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Orden de Compra → Factura</h3>
            <div class="flex items-end gap-6 mb-4">
                <div>
                    <p class="text-xs text-gray-500">Órdenes de Compra</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $conversion['ordenes_count'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400">S/ {{ number_format($conversion['ordenes_monto'] ?? 0, 2) }}</p>
                </div>
                <div class="text-gray-300 text-2xl">→</div>
                <div>
                    <p class="text-xs text-gray-500">Con factura emitida</p>
                    <p class="text-3xl font-bold text-indigo-700">{{ $conversion['ordenes_con_factura'] ?? 0 }}</p>
                </div>
            </div>
            @php $tasa2 = $conversion['tasa_oc_fac'] ?? 0; @endphp
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $tasa2 >= 50 ? 'bg-green-500' : ($tasa2 >= 25 ? 'bg-amber-400' : 'bg-red-400') }}"
                         style="width: {{ min($tasa2, 100) }}%"></div>
                </div>
                <span class="text-xl font-bold {{ $tasa2 >= 50 ? 'text-green-700' : ($tasa2 >= 25 ? 'text-amber-600' : 'text-red-600') }}">{{ $tasa2 }}%</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Tasa de facturación OC → Factura</p>
        </div>

        {{-- Funnel summary --}}
        <div class="md:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Embudo comercial completo</h3>
            <div class="flex items-center gap-3 text-sm">
                @php
                    $steps = [
                        ['label' => 'Cotizaciones', 'count' => $conversion['cotizaciones_count'] ?? 0, 'color' => 'bg-blue-100 text-blue-800'],
                        ['label' => 'OCs generadas', 'count' => $conversion['convertidas_oc'] ?? 0, 'color' => 'bg-indigo-100 text-indigo-800'],
                        ['label' => 'Facturas emitidas', 'count' => $conversion['ordenes_con_factura'] ?? 0, 'color' => 'bg-amber-100 text-amber-800'],
                    ];
                    $maxCount = max(array_column($steps, 'count'), 1);
                @endphp
                @foreach($steps as $i => $step)
                    @if($i > 0)<div class="text-gray-300 text-lg">›</div>@endif
                    <div class="flex-1 text-center">
                        <div class="rounded-lg p-3 {{ $step['color'] }} mb-1">
                            <p class="text-2xl font-bold">{{ $step['count'] }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ $step['label'] }}</p>
                        @if($i > 0 && $steps[$i-1]['count'] > 0)
                            <p class="text-xs font-medium text-gray-600 mt-0.5">{{ round(($step['count'] / $steps[$i-1]['count']) * 100, 1) }}%</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ===== Legacy tabs ===== --}}
    @if(in_array($tab, ['clientes', 'seguimientos', 'tareas', 'cotizaciones', 'correos', 'sin_contacto']) && $datos)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @if(in_array($tab, ['clientes', 'sin_contacto']))
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Empresa</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vendedor</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Último contacto</th>
                    @elseif($tab === 'seguimientos')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Detalle</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Usuario</th>
                    @elseif($tab === 'tareas')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tarea</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vencimiento</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Usuario</th>
                    @elseif($tab === 'cotizaciones')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Monto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    @elseif($tab === 'correos')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Destinatario</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($datos as $fila)
                <tr class="hover:bg-gray-50">
                    @if(in_array($tab, ['clientes', 'sin_contacto']))
                    <td class="px-4 py-3">
                        <a href="{{ route('clientes.show', $fila) }}" class="font-medium text-indigo-700 hover:underline">{{ $fila->razon_social }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $fila->contacto_principal }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $fila->estado_comercial->color() }}">{{ $fila->estado_comercial->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->vendedor?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs {{ !$fila->fecha_ultimo_contacto ? 'text-red-500' : 'text-gray-500' }}">
                        {{ $fila->fecha_ultimo_contacto?->format('d/m/Y') ?? 'Nunca' }}
                    </td>
                    @elseif($tab === 'seguimientos')
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->fecha_hora->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3"><a href="{{ route('clientes.show', $fila->cliente_id) }}" class="text-indigo-700 hover:underline font-medium">{{ $fila->cliente->razon_social }}</a></td>
                    <td class="px-4 py-3 text-gray-600">{{ $fila->tipo->label() }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $fila->detalle }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->usuario->name }}</td>
                    @elseif($tab === 'tareas')
                    @php $vencida = $fila->estaVencida(); @endphp
                    <td class="px-4 py-3 {{ $vencida ? 'text-red-700 font-medium' : 'text-gray-800' }}">{{ $fila->titulo }}</td>
                    <td class="px-4 py-3"><a href="{{ route('clientes.show', $fila->cliente_id) }}" class="text-indigo-700 hover:underline text-xs">{{ $fila->cliente->razon_social }}</a></td>
                    <td class="px-4 py-3 text-xs {{ $vencida ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $fila->fecha_vencimiento->format('d/m/Y') }}
                        @if($vencida)<span class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">Vencida</span>@endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $fila->estado === 'completada' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($fila->estado) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->usuario->name }}</td>
                    @elseif($tab === 'cotizaciones')
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $fila->codigo }}</td>
                    <td class="px-4 py-3"><a href="{{ route('clientes.show', $fila->cliente_id) }}" class="text-indigo-700 hover:underline font-medium">{{ $fila->cliente->razon_social }}</a></td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">S/ {{ number_format($fila->monto_total, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $fila->estado->color() }}">{{ $fila->estado->label() }}</span>
                    </td>
                    @elseif($tab === 'correos')
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $fila->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $fila->destinatario }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $fila->asunto }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $fila->estado_envio === 'enviado' ? 'bg-green-100 text-green-700' : ($fila->estado_envio === 'fallido' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($fila->estado_envio) }}
                        </span>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay datos para este reporte.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if(method_exists($datos, 'hasPages') && $datos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $datos->links() }}</div>
        @endif
    </div>
    @endif
</div>
