@props(['estado'])

@php
$colores = [
    'gray'   => 'bg-gray-100 text-gray-700',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'blue'   => 'bg-blue-100 text-blue-700',
    'indigo' => 'bg-indigo-100 text-indigo-700',
    'purple' => 'bg-purple-100 text-purple-700',
    'orange' => 'bg-orange-100 text-orange-700',
    'amber'  => 'bg-amber-100 text-amber-700',
    'green'  => 'bg-green-100 text-green-700',
    'red'    => 'bg-red-100 text-red-700',
    'slate'  => 'bg-slate-100 text-slate-700',
];
$clase = $colores[$estado->color()] ?? $colores['gray'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $clase }}">
    {{ $estado->label() }}
</span>
