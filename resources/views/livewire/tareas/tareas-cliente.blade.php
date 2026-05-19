<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Tareas pendientes</h3>

    @if($tareas->isEmpty())
    <p class="text-xs text-gray-400">Sin tareas pendientes.</p>
    @else
    <div class="space-y-2">
        @foreach($tareas as $tarea)
        @php $vencida = $tarea->estaVencida(); @endphp
        <div class="flex items-start gap-3 p-2 rounded-lg {{ $vencida ? 'bg-red-50' : 'bg-gray-50' }}">
            <button wire:click="completar({{ $tarea->id }})"
                    class="shrink-0 mt-0.5 w-4 h-4 rounded border {{ $vencida ? 'border-red-400' : 'border-gray-400' }} hover:bg-white transition-colors">
            </button>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium {{ $vencida ? 'text-red-700' : 'text-gray-700' }}">{{ $tarea->titulo }}</p>
                <p class="text-xs {{ $vencida ? 'text-red-500' : 'text-gray-400' }}">
                    {{ $tarea->fecha_vencimiento->format('d/m/Y') }}
                    @if($vencida) <span class="font-semibold">· Vencida</span> @endif
                </p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
