@props([
    'tone' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $tones = [
        'info' => [
            'border' => 'border-brand-200', 'bg' => 'bg-brand-50', 'text' => 'text-brand-700',
            'darkBorder' => 'dark:border-brand-500/30', 'darkBg' => 'dark:bg-brand-500/10', 'darkText' => 'dark:text-brand-400',
            'icon' => 'info', 'iconClass' => 'text-brand-500',
        ],
        'success' => [
            'border' => 'border-success-200', 'bg' => 'bg-success-50', 'text' => 'text-success-700',
            'darkBorder' => 'dark:border-success-500/30', 'darkBg' => 'dark:bg-success-500/10', 'darkText' => 'dark:text-success-400',
            'icon' => 'check', 'iconClass' => 'text-success-500',
        ],
        'warning' => [
            'border' => 'border-warning-200', 'bg' => 'bg-warning-50', 'text' => 'text-warning-700',
            'darkBorder' => 'dark:border-warning-500/30', 'darkBg' => 'dark:bg-warning-500/10', 'darkText' => 'dark:text-warning-400',
            'icon' => 'alert-triangle', 'iconClass' => 'text-warning-500',
        ],
        'error' => [
            'border' => 'border-error-200', 'bg' => 'bg-error-50', 'text' => 'text-error-700',
            'darkBorder' => 'dark:border-error-500/30', 'darkBg' => 'dark:bg-error-500/10', 'darkText' => 'dark:text-error-400',
            'icon' => 'alert-triangle', 'iconClass' => 'text-error-500',
        ],
    ];
    $cfg = $tones[$tone] ?? $tones['info'];
@endphp

<div
    @if ($dismissible) x-data="{ shown: true }" x-show="shown" x-transition.opacity @endif
    {{ $attributes->class([
        'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm',
        $cfg['border'], $cfg['bg'], $cfg['text'],
        $cfg['darkBorder'], $cfg['darkBg'], $cfg['darkText'],
    ]) }}
    role="alert">
    <x-admin.icon :name="$cfg['icon']" :class="'h-4 w-4 mt-0.5 shrink-0 '.$cfg['iconClass']" />
    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div @class(['mt-0.5' => $title])>{{ $slot }}</div>
    </div>
    @if ($dismissible)
        <button type="button" @click="shown = false" class="opacity-60 hover:opacity-100 transition" aria-label="Tutup">
            <x-admin.icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
