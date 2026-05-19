<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar por nombre o correo..."
                   class="rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-64">
        </div>
        <button wire:click="abrirCrear"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo usuario
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg border border-green-200 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded-lg border border-red-200 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Correo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Rol</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Desde</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($usuarios as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                            <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">Tú</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        @php $rol = $user->getRoleNames()->first(); @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $rol === 'administrador' ? 'bg-purple-100 text-purple-700' : ($rol === 'supervisor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($rol ?? 'sin rol') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="abrirEditar({{ $user->id }})"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</button>
                            @if($user->id !== auth()->id())
                            <button wire:click="eliminar({{ $user->id }})"
                                    wire:confirm="¿Eliminar a {{ $user->name }}? Esta acción no se puede deshacer."
                                    class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay usuarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($usuarios->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $usuarios->links() }}</div>
        @endif
    </div>

    {{-- Modal crear/editar --}}
    @if($mostrarModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">{{ $editandoId ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
                <button wire:click="$set('mostrarModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="guardar" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nombre completo *</label>
                    <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Correo electrónico *</label>
                    <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Contraseña {{ $editandoId ? '(dejar en blanco para no cambiar)' : '*' }}
                    </label>
                    <input wire:model="password" type="password" autocomplete="new-password"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rol *</label>
                    <select wire:model="rol" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                    @error('rol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('mostrarModal', false)"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        {{ $editandoId ? 'Guardar cambios' : 'Crear usuario' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
