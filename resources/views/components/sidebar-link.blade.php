@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-colors duration-150
          {{ $active
              ? 'bg-indigo-600 text-white'
              : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
    <span class="flex-shrink-0">{{ $icon }}</span>
    {{ $slot }}
</a>
