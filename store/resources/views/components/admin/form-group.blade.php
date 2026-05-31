@props([
    'label' => null,
    'for' => null,
    'name' => null,
    'hint' => null,
    'required' => false,
    'error' => null,
])

@php
    $errorMsg = $error ?? ($name ? ($errors->first($name) ?? null) : null);
    $controlId = $for ?? $name;
@endphp

<div {{ $attributes->class(['space-y-1.5']) }}>
    @if ($label)
        <label @if ($controlId) for="{{ $controlId }}" @endif class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
            @if ($required)
                <span class="text-error-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($hint && ! $errorMsg)
        <p class="mt-1.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif

    @if ($errorMsg)
        <p class="mt-1.5 text-theme-xs text-error-500">{{ $errorMsg }}</p>
    @endif
</div>
