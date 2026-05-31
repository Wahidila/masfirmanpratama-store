@props(['title', 'value', 'hint' => null, 'tone' => 'slate'])

@php
    $tones = [
        'primary' => 'from-primary-50 to-white text-primary-700 border-primary-100 dark:from-brand-500/10 dark:to-gray-900 dark:text-brand-300 dark:border-brand-500/20',
        'secondary' => 'from-secondary-50 to-white text-secondary-700 border-secondary-100 dark:from-secondary-500/10 dark:to-gray-900 dark:text-secondary-300 dark:border-secondary-500/20',
        'amber' => 'from-accent-50 to-white text-accent-700 border-accent-100 dark:from-warning-500/10 dark:to-gray-900 dark:text-warning-300 dark:border-warning-500/20',
        'slate' => 'from-slate-50 to-white text-slate-700 border-slate-100 dark:from-white/[0.04] dark:to-gray-900 dark:text-gray-300 dark:border-gray-800',
    ];
    $cls = $tones[$tone] ?? $tones['slate'];
@endphp

<div {{ $attributes->class([
    'rounded-2xl border bg-gradient-to-br p-5 shadow-theme-sm',
    $cls,
]) }}>
    <p class="text-xs font-medium uppercase tracking-wide opacity-80">{{ $title }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs opacity-70">{{ $hint }}</p>
    @endif
</div>
