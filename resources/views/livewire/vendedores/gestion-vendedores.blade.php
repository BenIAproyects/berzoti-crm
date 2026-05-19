<div class="flex gap-6">

    {{-- Panel izquierdo: lista de vendedores --}}
    <div class="w-72 flex-shrink-0 space-y-3">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Vendedores</p>
        @forelse($vendedores as $vendedor)
        <button wire:click="seleccionarVendedor({{ $vendedor->id }})"
                class="w-full text-left rounded-xl border p-4 transition-all
                       {{ $vendedorSeleccionadoId === $vendedor->id
                          ? 'bg-indigo-50 border-indigo-300 shadow-sm'
                          : 'bg-white border-gray-100 hover:border-indigo-200 hover:shadow-sm' }}">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr($vendedor->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $vendedor->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $vendedor->email }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $vendedor->total_clientes }} {{ $vendedor->total_clientes === 1 ? 'cliente' : 'clientes' }}
                </span>
            </div>
        </button>
        @empty
        <div class="bg-white rounded-xl border border-gray-100 p-6 text-center text-gray-400 text-sm">
            No hay vendedores registrados.
        </div>
        @endforelse
    </div>

    {{-- Panel derecho: clientes del vendedor seleccionado --}}
    <div class="flex-1 min-w-0">
        @if($vendedorSeleccionado)

        {{-- Mensaje éxito --}}
        @if($mensaje)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            {{ $mensaje }}
        </div>
        @endif

        {{-- Encabezado --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($vendedorSeleccionado->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">{{ $vendedorSeleccionado->name }}</h3>
                <p class="text-xs text-gray-400">{{ $vendedorSeleccionado->email }}</p>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-lg w-fit">
            <button wire:click="$set('tab','asignados')"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors
                           {{ $tab === 'asignados' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Asignados ({{ $totalAsignados }})
            </button>
            <button wire:click="$set('tab','rendimiento')"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors
                           {{ $tab === 'rendimiento' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Rendimiento
            </button>
            <button wire:click="$set('tab','disponibles')"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors
                           {{ $tab === 'disponibles' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Agregar clientes
            </button>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB: RENDIMIENTO                                           --}}
        {{-- ============================================================ --}}
        @if($tab === 'rendimiento')

        {{-- KPIs --}}
        @if($kpis)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $kpis['total'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Clientes asignados</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $kpis['contactados'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Contactados esta semana</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $kpis['con_cotizacion'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Con cotización enviada</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $kpis['ganados'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Ganados</p>
            </div>
        </div>
        @endif

        {{-- Filtro por estado --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mb-4">
            <select wire:model.live="filtroEstadoRendimiento"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los estados</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tabla rendimiento --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Empresa</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Cant. compra</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Precio ant.</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Cotización</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Último contacto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clientes as $cliente)
                    <tr wire:key="rend-{{ $cliente->id }}" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $cliente) }}"
                               class="font-medium text-indigo-700 hover:text-indigo-900 leading-tight">
                                {{ $cliente->razon_social }}
                            </a>
                            @if($cliente->contacto_principal)
                                <p class="text-xs text-gray-400">{{ $cliente->contacto_principal }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($cliente->cantidad_compra)
                                <span class="font-medium text-gray-800">{{ number_format($cliente->cantidad_compra) }}</span>
                                <span class="text-xs text-gray-400"> unid.</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">
                            @if($cliente->precio_ano_anterior)
                                S/. {{ number_format($cliente->precio_ano_anterior, 2) }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <x-estado-badge :estado="$cliente->estado_comercial" />
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($cliente->cotizaciones_enviadas > 0)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                    Sí
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            @if($cliente->fecha_ultimo_contacto)
                                <span class="{{ $cliente->fecha_ultimo_contacto->lt(now()->subDays(7)) ? 'text-red-500 font-medium' : '' }}">
                                    {{ $cliente->fecha_ultimo_contacto->format('d/m/Y') }}
                                </span>
                                <p class="text-gray-300">{{ $cliente->fecha_ultimo_contacto->diffForHumans() }}</p>
                            @else
                                <span class="text-red-400 font-medium">Sin contacto</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No hay clientes asignados a este vendedor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($clientes && $clientes->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $clientes->links() }}</div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- TABS: ASIGNADOS y DISPONIBLES                               --}}
        {{-- ============================================================ --}}
        @else

        {{-- Filtros --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input wire:model.live.debounce.300ms="busqueda"
                           type="text" placeholder="Buscar por nombre, RUC, correo..."
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <select wire:model.live="filtroTipo"
                            class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Tabla clientes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Empresa</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                        @if($tab === 'asignados')
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                        @endif
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clientes as $cliente)
                    <tr wire:key="cli-{{ $tab }}-{{ $cliente->id }}" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('clientes.show', $cliente) }}"
                               class="font-medium text-indigo-700 hover:text-indigo-900">
                                {{ $cliente->razon_social }}
                            </a>
                            @if($cliente->ruc)
                                <p class="text-xs text-gray-400">{{ $cliente->ruc }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $cliente->contacto_principal }}
                            @if($cliente->correo)
                                <p class="text-xs text-gray-400">{{ $cliente->correo }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $cliente->tipo_cliente->label() }}
                        </td>
                        @if($tab === 'asignados')
                        <td class="px-4 py-3">
                            <x-estado-badge :estado="$cliente->estado_comercial" />
                        </td>
                        @endif
                        <td class="px-4 py-3 text-right">
                            @if($tab === 'asignados')
                            <button
                                x-data
                                x-on:click="if(confirm('¿Quitar este cliente del vendedor?')) $wire.quitar({{ $cliente->id }})"
                                class="text-xs text-red-600 hover:text-red-800 font-medium">
                                Quitar
                            </button>
                            @else
                            <button
                                wire:click="asignar({{ $cliente->id }})"
                                wire:loading.class="opacity-50 pointer-events-none"
                                wire:target="asignar({{ $cliente->id }})"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                + Asignar
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            {{ $tab === 'asignados'
                                ? 'Este vendedor no tiene clientes asignados.'
                                : 'No hay clientes disponibles para asignar.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($clientes && $clientes->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $clientes->links() }}</div>
            @endif
        </div>

        @endif

        @else
        {{-- Estado vacío --}}
        <div class="bg-white rounded-xl border border-gray-100 h-64 flex flex-col items-center justify-center text-gray-400">
            <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <p class="text-sm">Selecciona un vendedor para gestionar sus clientes</p>
        </div>
        @endif
    </div>

</div>
