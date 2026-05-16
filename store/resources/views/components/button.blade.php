@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'right',
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 text-white shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40',
        'secondary' => 'bg-secondary-600 hover:bg-secondary-700 text-white shadow-lg shadow-secondary-500/30 hover:shadow-secondary-500/40',
        'outline' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm hover:shadow-md',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
    ];

    $variantClasses = $variants[$variant] ?? $variants['primary'];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];

    $base = 'ripple inline-flex items-center justify-center gap-2 rounded-full font-semibold transition-all transform hover:-translate-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2';

    $classes = trim("{$base} {$variantClasses} {$sizeClasses}");
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($icon && $iconPosition === 'left')
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($icon && $iconPosition === 'left')
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        @endif
    </button>
@endif
