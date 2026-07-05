<x-app-layout>
    <x-slot name="title">{{ $campana->nombre }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('campanas.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Volver a campañas</a>
    </div>

    {{-- Cabecera --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $campana->nombre }}</h2>
                    <x-campana-estado-badge :estado="$campana->estado" />
                </div>
                @if($campana->descripcion)
                    <p class="text-sm text-gray-500 mt-1">{{ $campana->descripcion }}</p>
                @endif

                <div class="flex flex-wrap gap-4 mt-3 text-xs text-gray-400">
                    @if($campana->fecha_inicio)
                    <span>Inicio: <strong class="text-gray-600">{{ $campana->fecha_inicio->format('d/m/Y') }}</strong></span>
                    @endif
                    @if($campana->fecha_fin)
                    <span>Fin: <strong class="text-gray-600">{{ $campana->fecha_fin->format('d/m/Y') }}</strong></span>
                    @endif
                    <span>Creada por: <strong class="text-gray-600">{{ $campana->creador->name }}</strong></span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @can('campanas.editar')
                <a href="{{ route('campanas.edit', $campana) }}"
                   class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    Editar
                </a>
                @endcan
                @can('campanas.eliminar')
                <form method="POST" action="{{ route('campanas.destroy', $campana) }}"
                      onsubmit="return confirm('¿Eliminar la campaña \'{{ addslashes($campana->nombre) }}\'? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>

        @if($campana->objetivo_comercial)
        <div class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
            <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wide mb-1">Objetivo comercial</p>
            <p class="text-sm text-indigo-800">{{ $campana->objetivo_comercial }}</p>
        </div>
        @endif
    </div>

    {{-- KPIs rápidos --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalClientes = $campana->clientes()->count();
            $porEstado = $campana->clientes()
                ->selectRaw('clientes.estado_comercial, count(*) as total')
                ->groupBy('clientes.estado_comercial')
                ->pluck('total', 'clientes.estado_comercial');
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-indigo-700">{{ $totalClientes }}</p>
            <p class="text-xs text-gray-500 mt-1">Total clientes</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $porEstado['cerrado_ganado'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Cerrados ganados</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $porEstado['interesado'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Interesados</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $porEstado['cerrado_perdido'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Cerrados perdidos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Gestión de clientes --}}
        <div class="lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-700 mb-4">Clientes en esta campaña</h3>
            @livewire('campanas.gestionar-clientes', ['campana' => $campana])
        </div>

        {{-- Envío masivo --}}
        <div>
            @can('correos.enviar')
            @livewire('correos.envio-masivo', ['campana' => $campana])
            @endcan
        </div>
    </div>
</x-app-layout>
