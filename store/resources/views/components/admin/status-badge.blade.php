@props(['status' => ''])

@php
    $statusColors = match (strtolower($status)) {
        'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
        'paid' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400',
        'partial_paid' => 'bg-accent-50 text-accent-700 dark:bg-warning-500/15 dark:text-warning-300',
        'shipped' => 'bg-secondary-50 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-400',
        'completed' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
        'cancelled', 'refunded' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
        default => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium', $statusColors]) }}>
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
