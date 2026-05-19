<div class="inline-flex items-center gap-3">
    <button wire:click="sincronizar"
            wire:loading.attr="disabled"
            wire:target="sincronizar"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">
        <svg wire:loading wire:target="sincronizar" class="w-4 h-4 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        <svg wire:loading.remove wire:target="sincronizar" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Sincronizar aperturas
    </button>

    @if($mensaje)
    <span class="text-sm {{ str_starts_with($mensaje, 'Error') ? 'text-red-600' : 'text-green-600' }}">
        {{ $mensaje }}
    </span>
    @endif
</div>
