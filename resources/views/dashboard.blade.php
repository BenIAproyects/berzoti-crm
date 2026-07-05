<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-gray-800">Panel general</h1>
        <p class="text-sm text-gray-500 mt-0.5">Resumen de la actividad comercial</p>
    </div>

    {{-- KPIs principales --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Total clientes</p>
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($totalClientes) }}</p>
            <p class="text-xs text-green-600 mt-1">+{{ $clientesNuevos }} este mes</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Correos enviados</p>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($correosTotal) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $correosMes }} este mes</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Tareas pendientes</p>
                <div class="w-8 h-8 {{ $tareasVencidas > 0 ? 'bg-red-100' : 'bg-amber-100' }} rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $tareasVencidas > 0 ? 'text-red-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($tareasPendientes) }}</p>
            @if($tareasVencidas > 0)
            <p class="text-xs text-red-600 mt-1 font-medium">{{ $tareasVencidas }} vencidas</p>
            @else
            <p class="text-xs text-gray-500 mt-1">Al día</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Ventas aprobadas</p>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $cotizacionesAprobadas }}</p>
            <p class="text-xs text-green-600 mt-1">S/ {{ number_format($montoAprobado, 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Embudo comercial --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Embudo comercial</h3>
            @php
                $estadosEmbudo = [
                    ['key' => 'nuevo',              'label' => 'Nuevo',              'color' => 'bg-gray-400'],
                    ['key' => 'contacto_pendiente', 'label' => 'Contacto pendiente', 'color' => 'bg-blue-400'],
                    ['key' => 'correo_enviado',     'label' => 'Correo enviado',     'color' => 'bg-indigo-400'],
                    ['key' => 'en_seguimiento',     'label' => 'En seguimiento',     'color' => 'bg-purple-400'],
                    ['key' => 'interesado',         'label' => 'Interesado',         'color' => 'bg-yellow-400'],
                    ['key' => 'cotizacion_enviada', 'label' => 'Cotización enviada', 'color' => 'bg-orange-400'],
                    ['key' => 'negociacion',        'label' => 'Negociación',        'color' => 'bg-amber-400'],
                    ['key' => 'cerrado_ganado',     'label' => 'Cerrado ganado',     'color' => 'bg-green-500'],
                    ['key' => 'cerrado_perdido',    'label' => 'Cerrado perdido',    'color' => 'bg-red-400'],
                    ['key' => 'no_responde',        'label' => 'No responde',        'color' => 'bg-gray-300'],
                ];
                $maxEmbudo = $embudo->max() ?: 1;
            @endphp
            <div class="space-y-2">
                @foreach($estadosEmbudo as $e)
                @php $qty = $embudo->get($e['key'], 0); @endphp
                @if($qty > 0 || in_array($e['key'], ['nuevo', 'cerrado_ganado', 'cerrado_perdido']))
                <div class="flex items-center gap-3">
                    <p class="text-xs text-gray-500 w-36 shrink-0">{{ $e['label'] }}</p>
                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                        <div class="{{ $e['color'] }} rounded-full h-2 transition-all"
                             style="width: {{ $qty > 0 ? max(2, round($qty / $maxEmbudo * 100)) : 0 }}%"></div>
                    </div>
                    <p class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $qty }}</p>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Resumen rápido --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Actividad reciente</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Seguimientos esta semana</span>
                        <span class="font-semibold text-gray-800">{{ $seguimientosSemana }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Campañas activas</span>
                        <span class="font-semibold text-gray-800">{{ $campanasActivas }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Sin contacto +7 días</span>
                        <span class="font-semibold {{ $sinSeguimiento > 0 ? 'text-amber-600' : 'text-gray-800' }}">{{ $sinSeguimiento }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Cotizaciones enviadas</span>
                        <span class="font-semibold text-gray-800">{{ $cotizacionesEnviadas }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Cotizaciones rechazadas</span>
                        <span class="font-semibold text-red-600">{{ $cotizacionesRechazadas }}</span>
                    </div>
                </div>
            </div>

            @if($tareasVencidas > 0)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-2 text-red-700">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm font-semibold">{{ $tareasVencidas }} tarea{{ $tareasVencidas > 1 ? 's' : '' }} vencida{{ $tareasVencidas > 1 ? 's' : '' }}</p>
                </div>
                <a href="{{ route('tareas.index') }}" class="mt-2 inline-block text-xs text-red-700 underline">Ver tareas →</a>
            </div>
            @endif
        </div>
    </div>

    {{-- KPIs comerciales --}}
    @if(!empty($kpiComercial))
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Módulo Comercial</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <a href="{{ route('ordenes-compra.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-indigo-200 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500">OCs Abiertas</p>
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $kpiComercial['ocs_abiertas'] }}</p>
                <p class="text-xs text-gray-500 mt-1">S/ {{ number_format($kpiComercial['ocs_total'], 0) }}</p>
            </a>

            <a href="{{ route('facturas.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-amber-200 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500">Por Cobrar</p>
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">S/ {{ number_format($kpiComercial['saldo_cobrar'], 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">saldo pendiente</p>
            </a>

            <a href="{{ route('facturas.index') }}" class="bg-white rounded-xl shadow-sm border border-{{ $kpiComercial['facturas_vencidas'] > 0 ? 'red' : 'gray' }}-100 p-5 hover:border-red-200 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500">Facturas Vencidas</p>
                    <div class="w-8 h-8 {{ $kpiComercial['facturas_vencidas'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 {{ $kpiComercial['facturas_vencidas'] > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold {{ $kpiComercial['facturas_vencidas'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $kpiComercial['facturas_vencidas'] }}</p>
                <p class="text-xs {{ $kpiComercial['facturas_vencidas'] > 0 ? 'text-red-500' : 'text-gray-500' }} mt-1">
                    S/ {{ number_format($kpiComercial['monto_vencido'], 0) }} vencido
                </p>
            </a>

            @if($kpiComercial['guias_pendientes'] !== null)
            <a href="{{ route('guias-remision.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-teal-200 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-500">Guías Pendientes</p>
                    <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $kpiComercial['guias_pendientes'] }}</p>
                <p class="text-xs text-gray-500 mt-1">pendientes de entrega</p>
            </a>
            @endif

        </div>
    </div>
    @endif

    {{-- Top vendedores (solo admin/supervisor) --}}
    @if($esAdmin && $topVendedores->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Clientes por vendedor</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($topVendedores as $v)
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-indigo-600">{{ $v->total }}</p>
                <p class="text-xs text-gray-500 mt-1 truncate">{{ $v->vendedor?->name ?? 'Sin nombre' }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</x-app-layout>
