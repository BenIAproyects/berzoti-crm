<div class="space-y-4">

    {{-- Cabecera de la ficha --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $cliente->razon_social }}</h2>
                    <x-estado-badge :estado="$cliente->estado_comercial" />
                    @if($cliente->segmento)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $cliente->segmento->value === 'vip' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $cliente->segmento->label() }}
                        </span>
                    @endif
                    @if($cliente->fuente)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $cliente->fuente->value === 'lima' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $cliente->fuente->label() }}
                        </span>
                    @endif
                </div>
                @if($cliente->nombre_comercial)
                    <p class="text-sm text-gray-500">{{ $cliente->nombre_comercial }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">
                    Código: {{ $cliente->codigo }}
                    @if($cliente->ruc) &bull; RUC: {{ $cliente->ruc }} @endif
                    @if($cliente->zona) &bull; Zona: {{ $cliente->zona }} @endif
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @can('correos.enviar')
                @livewire('correos.enviar-correo', ['cliente' => $cliente])
                @endcan
                @can('clientes.editar')
                <a href="{{ route('clientes.edit', $cliente) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Editar
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Barra de pestañas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <nav class="flex min-w-max">
            @php
                $tabs = [
                    'datos'         => 'Datos',
                    'contactos'     => 'Contactos',
                    'correos'       => 'Correos',
                    'telefonos'     => 'Teléfonos',
                    'seguimientos'  => 'Seguimientos',
                    'cotizaciones'  => 'Cotizaciones',
                    'ordenes_compra' => 'Órdenes de Compra',
                    'facturas'       => 'Facturas',
                    'guias_remision' => 'Guías de Remisión',
                    'pagos'           => 'Pagos',
                    'historial'       => 'Historial Comercial',
                ];
            @endphp
            @foreach($tabs as $key => $label)
            <button
                wire:click="setTab('{{ $key }}')"
                class="px-5 py-3.5 text-sm whitespace-nowrap transition-colors focus:outline-none
                    {{ $tabActiva === $key
                        ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold'
                        : 'text-gray-500 hover:text-gray-700 hover:border-b-2 hover:border-gray-300' }}">
                {{ $label }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- PESTAÑA: Datos --}}
    @if($tabActiva === 'datos')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Empresa</h3>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400">Tipo</dt>
                        <dd class="text-gray-800 font-medium">{{ $cliente->tipo_cliente->label() }}</dd>
                    </div>
                    @if($cliente->sector)
                    <div>
                        <dt class="text-gray-400">Sector</dt>
                        <dd class="text-gray-800">{{ $cliente->sector }}</dd>
                    </div>
                    @endif
                    @if($cliente->origen)
                    <div>
                        <dt class="text-gray-400">Origen</dt>
                        <dd class="text-gray-800">{{ $cliente->origen }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-400">Prioridad</dt>
                        <dd class="font-medium {{ match($cliente->prioridad) { 'alta' => 'text-red-600', 'media' => 'text-amber-600', default => 'text-gray-600' } }}">
                            {{ ucfirst($cliente->prioridad) }}
                        </dd>
                    </div>
                    @if($cliente->fuente)
                    <div>
                        <dt class="text-gray-400">Fuente</dt>
                        <dd class="text-gray-800">{{ $cliente->fuente->label() }}</dd>
                    </div>
                    @endif
                    @if($cliente->zona)
                    <div>
                        <dt class="text-gray-400">Zona</dt>
                        <dd class="text-gray-800">{{ $cliente->zona }}</dd>
                    </div>
                    @endif
                    @if($cliente->segmento)
                    <div>
                        <dt class="text-gray-400">Segmento</dt>
                        <dd class="text-gray-800">{{ $cliente->segmento->label() }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Contacto principal</h3>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    @if($cliente->contacto_principal)
                    <div>
                        <dt class="text-gray-400">Nombre</dt>
                        <dd class="text-gray-800 font-medium">{{ $cliente->contacto_principal }}</dd>
                    </div>
                    @endif
                    @if($cliente->cargo_contacto)
                    <div>
                        <dt class="text-gray-400">Cargo</dt>
                        <dd class="text-gray-800">{{ $cliente->cargo_contacto }}</dd>
                    </div>
                    @endif
                    @if($cliente->telefono)
                    <div>
                        <dt class="text-gray-400">Teléfono</dt>
                        <dd class="text-gray-800">{{ $cliente->telefono }}</dd>
                    </div>
                    @endif
                    @if($cliente->whatsapp)
                    <div>
                        <dt class="text-gray-400">WhatsApp</dt>
                        <dd class="text-gray-800">{{ $cliente->whatsapp }}</dd>
                    </div>
                    @endif
                    @if($cliente->correo)
                    <div>
                        <dt class="text-gray-400">Correo</dt>
                        <dd class="text-gray-800">{{ $cliente->correo }}</dd>
                    </div>
                    @endif
                    @if($cliente->correo_secundario)
                    <div>
                        <dt class="text-gray-400">Correo secundario</dt>
                        <dd class="text-gray-800">{{ $cliente->correo_secundario }}</dd>
                    </div>
                    @endif
                    @if(!$cliente->contacto_principal && !$cliente->telefono && !$cliente->correo)
                    <div class="col-span-2 text-gray-400 text-xs italic">
                        Sin contacto principal. Usa la pestaña "Contactos" para agregar.
                    </div>
                    @endif
                </dl>
            </div>

            @if($cliente->departamento || $cliente->direccion)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Ubicación</h3>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    @if($cliente->departamento)
                    <div>
                        <dt class="text-gray-400">Departamento</dt>
                        <dd class="text-gray-800">{{ $cliente->departamento }}</dd>
                    </div>
                    @endif
                    @if($cliente->provincia)
                    <div>
                        <dt class="text-gray-400">Provincia</dt>
                        <dd class="text-gray-800">{{ $cliente->provincia }}</dd>
                    </div>
                    @endif
                    @if($cliente->distrito)
                    <div>
                        <dt class="text-gray-400">Distrito</dt>
                        <dd class="text-gray-800">{{ $cliente->distrito }}</dd>
                    </div>
                    @endif
                    @if($cliente->direccion)
                    <div class="col-span-2">
                        <dt class="text-gray-400">Dirección</dt>
                        <dd class="text-gray-800">{{ $cliente->direccion }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            @if($cliente->observaciones)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Observaciones</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $cliente->observaciones }}</p>
            </div>
            @endif

        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Comercial</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400">Vendedor</dt>
                        <dd class="text-gray-800 font-medium">{{ $cliente->vendedor?->name ?? 'Sin asignar' }}</dd>
                    </div>
                    @if($cliente->cantidad_compra)
                    <div>
                        <dt class="text-gray-400">Cantidad habitual</dt>
                        <dd class="text-gray-800 font-medium">{{ number_format($cliente->cantidad_compra) }} unid.</dd>
                    </div>
                    @endif
                    @if($cliente->precio_ano_anterior)
                    <div>
                        <dt class="text-gray-400">Precio año anterior</dt>
                        <dd class="text-gray-800">S/. {{ number_format($cliente->precio_ano_anterior, 2) }}</dd>
                    </div>
                    @endif
                    @if($cliente->mes_contacto)
                    <div>
                        <dt class="text-gray-400">Mes de contacto</dt>
                        <dd class="text-gray-800 capitalize">{{ $cliente->mes_contacto }}</dd>
                    </div>
                    @endif
                    @if($cliente->fecha_ultimo_contacto)
                    <div>
                        <dt class="text-gray-400">Último contacto</dt>
                        <dd class="text-gray-800">{{ $cliente->fecha_ultimo_contacto->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($cliente->fecha_proximo_contacto)
                    <div>
                        <dt class="text-gray-400">Próximo contacto</dt>
                        <dd class="{{ $cliente->fecha_proximo_contacto->isPast() ? 'text-red-600 font-medium' : 'text-gray-800' }}">
                            {{ $cliente->fecha_proximo_contacto->format('d/m/Y') }}
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-400">Registrado</dt>
                        <dd class="text-gray-600 text-xs">{{ $cliente->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            @livewire('tareas.tareas-cliente', ['cliente' => $cliente], key('tareas-'.$cliente->id))
        </div>

    </div>
    @endif

    {{-- PESTAÑA: Contactos --}}
    @if($tabActiva === 'contactos')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Contactos</h3>
        @livewire('clientes.cliente-contactos', ['cliente' => $cliente], key('contactos-'.$cliente->id))
    </div>
    @endif

    {{-- PESTAÑA: Correos --}}
    @if($tabActiva === 'correos')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Correos electrónicos</h3>
        @livewire('clientes.cliente-correos', ['cliente' => $cliente], key('correos-tab-'.$cliente->id))
    </div>
    @endif

    {{-- PESTAÑA: Teléfonos --}}
    @if($tabActiva === 'telefonos')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Teléfonos</h3>
        @livewire('clientes.cliente-telefonos', ['cliente' => $cliente], key('telefonos-'.$cliente->id))
    </div>
    @endif

    {{-- PESTAÑA: Seguimientos --}}
    @if($tabActiva === 'seguimientos')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Seguimientos</h3>
        @can('seguimientos.crear')
        @livewire('seguimientos.formulario-seguimiento', ['cliente' => $cliente], key('form-seg-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('seguimientos.lista-seguimientos', ['cliente' => $cliente], key('lista-seg-'.$cliente->id))
        </div>
    </div>
    @endif

    {{-- PESTAÑA: Cotizaciones --}}
    @if($tabActiva === 'cotizaciones')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Cotizaciones</h3>
        @can('cotizaciones.crear')
        @livewire('cotizaciones.formulario-cotizacion', ['cliente' => $cliente], key('form-cot-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('cotizaciones.lista-cotizaciones', ['cliente' => $cliente], key('lista-cot-'.$cliente->id))
        </div>
    </div>
    @endif

    {{-- PESTAÑA: Órdenes de Compra --}}
    @if($tabActiva === 'ordenes_compra')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Órdenes de Compra</h3>
        @can('ordenes.crear')
        @livewire('ordenes-compra.formulario-orden-compra', ['cliente' => $cliente], key('form-oc-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('ordenes-compra.lista-ordenes-compra-cliente', ['cliente' => $cliente], key('lista-oc-'.$cliente->id))
        </div>
    </div>
    @endif

    {{-- PESTAÑA: Facturas --}}
    @if($tabActiva === 'facturas')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Facturas</h3>
        @can('facturas.crear')
        @livewire('facturas.formulario-factura', ['cliente' => $cliente], key('form-fac-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('facturas.lista-facturas-cliente', ['cliente' => $cliente], key('lista-fac-'.$cliente->id))
        </div>
    </div>
    @endif

    {{-- PESTAÑA: Guías de Remisión --}}
    @if($tabActiva === 'guias_remision')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Guías de Remisión</h3>
        @can('guias.crear')
        @livewire('guias-remision.formulario-guia-remision', ['cliente' => $cliente], key('form-gr-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('guias-remision.lista-guias-remision-cliente', ['cliente' => $cliente], key('lista-gr-'.$cliente->id))
        </div>
    </div>
    @endif

    {{-- PESTAÑA: Pagos --}}
    @if($tabActiva === 'pagos')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Pagos</h3>
        @can('pagos.crear')
        @livewire('pagos.formulario-pago', ['cliente' => $cliente], key('form-pago-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('pagos.lista-pagos-cliente', ['cliente' => $cliente], key('lista-pago-'.$cliente->id))
        </div>
    </div>
    @endif

    {{-- PESTAÑA: Historial Comercial --}}
    @if($tabActiva === 'historial')
    @livewire('clientes.historial-comercial', ['cliente' => $cliente], key('historial-'.$cliente->id))
    @endif

</div>
