<div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <input wire:model.live.debounce.300ms="busqueda" type="text"
               placeholder="Buscar plantilla por nombre o asunto..."
               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Creada por</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($plantillas as $plantilla)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $plantilla->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $plantilla->asunto }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleActivo({{ $plantilla->id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                       {{ $plantilla->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $plantilla->activo ? 'Activa' : 'Inactiva' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $plantilla->creador->name }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            @can('plantillas.editar')
                            <a href="{{ route('plantillas.edit', $plantilla) }}"
                               class="text-xs text-amber-600 hover:text-amber-800 font-medium">Editar</a>
                            @endcan
                            <button wire:click="duplicar({{ $plantilla->id }})"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Duplicar
                            </button>
                            @can('plantillas.eliminar')
                            <button wire:click="eliminar({{ $plantilla->id }})"
                                    wire:confirm="¿Eliminar esta plantilla?"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium">
                                Eliminar
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                        No hay plantillas creadas aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($plantillas->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $plantillas->links() }}</div>
        @endif
    </div>
</div>
