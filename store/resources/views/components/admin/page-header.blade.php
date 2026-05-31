@props([
    'title',
    'subtitle' => null,
    'breadcrumb' => [],
])

<header {{ $attributes->class(['mb-8']) }}>
    @if (! empty($breadcrumb))
        <div class="mb-2">
            <x-admin.breadcrumb :items="$breadcrumb" />
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</header>
