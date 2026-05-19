@props(['title', 'value', 'hint' => null, 'tone' => 'slate'])

@php
    $tones = [
        'primary' => 'from-primary-50 to-white text-primary-700 border-primary-100',
        'secondary' => 'from-secondary-50 to-white text-secondary-700 border-secondary-100',
        'amber' => 'from-accent-50 to-white text-accent-700 border-accent-100',
        'slate' => 'from-slate-50 to-white text-slate-700 border-slate-100',
    ];
    $cls = $tones[$tone] ?? $tones['slate'];
@endphp

<div {{ $attributes->class([
    'rounded-2xl border bg-gradient-to-br p-5 shadow-sm',
    $cls,
]) }}>
    <p class="text-xs font-medium uppercase tracking-wide opacity-80">{{ $title }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs opacity-70">{{ $hint }}</p>
    @endif
</div>
