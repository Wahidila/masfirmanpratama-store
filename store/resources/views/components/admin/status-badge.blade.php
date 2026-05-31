@props([
    'status' => '',
    'label' => null,
])

@php
    $statusLabelMap = [
        'pending' => 'Pending',
        'partial_paid' => 'Cicilan',
        'paid' => 'Lunas',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Batal',
        'refunded' => 'Refund',
    ];

    $displayLabel = $label ?? $statusLabelMap[strtolower($status)] ?? ucfirst(str_replace('_', ' ', $status));

    [$bgClasses, $ringClasses] = match (strtolower($status)) {
        'pending' => [
            'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
            'ring-warning-300 dark:ring-warning-500/40',
        ],
        'partial_paid' => [
            'bg-accent-50 text-accent-700 dark:bg-warning-500/15 dark:text-warning-300',
            'ring-accent-300 dark:ring-warning-500/40',
        ],
        'paid' => [
            'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400',
            'ring-brand-300 dark:ring-brand-500/40',
        ],
        'shipped' => [
            'bg-secondary-50 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-400',
            'ring-secondary-300 dark:ring-secondary-500/40',
        ],
        'completed' => [
            'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
            'ring-success-300 dark:ring-success-500/40',
        ],
        'cancelled', 'refunded' => [
            'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
            'ring-error-300 dark:ring-error-500/40',
        ],
        default => [
            'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300',
            'ring-gray-300 dark:ring-gray-600',
        ],
    };
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
    $bgClasses,
    $ringClasses,
]) }}>
    {{ $displayLabel }}
</span>
