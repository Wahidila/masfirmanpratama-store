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
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</header>
