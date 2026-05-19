@props(['estado'])

@php
$clases = match($estado->value) {
    'borrador'  => 'bg-gray-100 text-gray-700',
    'activa'    => 'bg-green-100 text-green-700',
    'cerrada'   => 'bg-red-100 text-red-700',
    'archivada' => 'bg-slate-100 text-slate-500',
    default     => 'bg-gray-100 text-gray-600',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $clases }}">
    {{ $estado->label() }}
</span>
