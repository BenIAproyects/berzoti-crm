<div>
    {{-- Mensaje de éxito --}}
    @if($mensaje)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
        {{ $mensaje }}
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-lg w-fit">
        <button wire:click="$set('tab','asignados')"
                class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors
                       {{ $tab === 'asignados' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Asignados ({{ $totalAsignados }})
        </button>
        <button wire:click="$set('tab','disponibles')"
                class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors
                       {{ $tab === 'disponibles' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Agregar clientes
        </button>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input wire:model.live.debounce.300ms="busqueda"
                       type="text" placeholder="Buscar por nombre, RUC..."
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

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Empresa</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                    @if($tab === 'asignados')
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado comercial</th>
                    @endif
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($clientes as $cliente)
                <tr wire:key="cliente-{{ $tab }}-{{ $cliente->id }}" class="hover:bg-gray-50">
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
                            x-on:click="if(confirm('¿Quitar este cliente de la campaña?')) $wire.quitar({{ $cliente->id }})"
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
                        {{ $tab === 'asignados' ? 'No hay clientes asignados a esta campaña.' : 'No hay clientes disponibles para agregar.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($clientes->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $clientes->links() }}</div>
        @endif
    </div>
</div>
