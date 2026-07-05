<div>
    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-[200px]">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text" placeholder="Buscar campaña..."
                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select wire:model.live="filtroEstado"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tarjetas de campañas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($campanas as $campana)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-3">
            <div class="flex items-start justify-between gap-2">
                <h3 class="font-semibold text-gray-800 leading-tight">{{ $campana->nombre }}</h3>
                <x-campana-estado-badge :estado="$campana->estado" />
            </div>

            @if($campana->descripcion)
            <p class="text-sm text-gray-500 line-clamp-2">{{ $campana->descripcion }}</p>
            @endif

            <div class="flex items-center gap-4 text-xs text-gray-400">
                @if($campana->fecha_inicio)
                <span>Inicio: {{ $campana->fecha_inicio->format('d/m/Y') }}</span>
                @endif
                @if($campana->fecha_fin)
                <span>Fin: {{ $campana->fecha_fin->format('d/m/Y') }}</span>
                @endif
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <span class="text-sm font-medium text-indigo-700">
                    {{ $campana->clientes_count }} cliente(s)
                </span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('campanas.show', $campana) }}"
                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                        Ver →
                    </a>
                    @can('campanas.editar')
                    <a href="{{ route('campanas.edit', $campana) }}"
                       class="text-xs font-medium text-gray-500 hover:text-gray-700">
                        Editar
                    </a>
                    @endcan
                    @can('campanas.eliminar')
                    <button wire:click="eliminar({{ $campana->id }})"
                            wire:confirm="¿Eliminar la campaña '{{ addslashes($campana->nombre) }}'? Esta acción no se puede deshacer."
                            class="text-xs font-medium text-red-500 hover:text-red-700">
                        Eliminar
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 xl:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
            No se encontraron campañas.
        </div>
        @endforelse
    </div>

    @if($campanas->hasPages())
    <div class="mt-4">{{ $campanas->links() }}</div>
    @endif
</div>
