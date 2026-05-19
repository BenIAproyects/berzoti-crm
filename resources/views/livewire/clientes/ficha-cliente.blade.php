<div class="space-y-6">

    {{-- Cabecera de la ficha --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $cliente->razon_social }}</h2>
                    <x-estado-badge :estado="$cliente->estado_comercial" />
                </div>
                @if($cliente->nombre_comercial)
                    <p class="text-sm text-gray-500">{{ $cliente->nombre_comercial }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">Código: {{ $cliente->codigo }} @if($cliente->ruc) &bull; RUC: {{ $cliente->ruc }} @endif</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna izquierda --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Datos empresa --}}
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
                </dl>
            </div>

            {{-- Contacto --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Contacto principal</h3>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400">Nombre</dt>
                        <dd class="text-gray-800 font-medium">{{ $cliente->contacto_principal }}</dd>
                    </div>
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
                </dl>
            </div>

            {{-- Ubicación --}}
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

            {{-- Observaciones --}}
            @if($cliente->observaciones)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Observaciones</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $cliente->observaciones }}</p>
            </div>
            @endif

        </div>

        {{-- Columna derecha --}}
        <div class="space-y-6">

            {{-- Datos comerciales --}}
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

            {{-- Tareas pendientes del cliente --}}
            @livewire('tareas.tareas-cliente', ['cliente' => $cliente], key('tareas-'.$cliente->id))

        </div>
    </div>

    {{-- Seguimientos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-700">Seguimientos</h3>
        </div>
        @can('seguimientos.crear')
        @livewire('seguimientos.formulario-seguimiento', ['cliente' => $cliente], key('form-seg-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('seguimientos.lista-seguimientos', ['cliente' => $cliente], key('lista-seg-'.$cliente->id))
        </div>
    </div>

    {{-- Cotizaciones --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-700">Cotizaciones</h3>
        </div>
        @can('cotizaciones.crear')
        @livewire('cotizaciones.formulario-cotizacion', ['cliente' => $cliente], key('form-cot-'.$cliente->id))
        @endcan
        <div class="mt-4">
            @livewire('cotizaciones.lista-cotizaciones', ['cliente' => $cliente], key('lista-cot-'.$cliente->id))
        </div>
    </div>
</div>
