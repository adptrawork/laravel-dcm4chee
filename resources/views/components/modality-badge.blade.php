@props(['label', 'color' => 'gray'])
@php
    $colors = [
        'warning' => 'bg-yellow-100 text-yellow-700',
        'info' => 'bg-blue-100 text-blue-700',
        'success' => 'bg-green-100 text-green-700',
        'danger' => 'bg-red-100 text-red-700',
        'gray' => 'bg-gray-100 text-gray-600',
    ];
    $class = $colors[$color] ?? $colors['gray'];
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $class }}">{{ $label }}</span>