@props(['items' => []])

@php
    // Auto-prepend Dashboard kalau current route bukan dashboard.
    $resolved = collect($items);
    if (! request()->routeIs('admin.dashboard') && $resolved->isEmpty() === false) {
        $resolved = $resolved->prepend(['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home']);
    }
@endphp

@if ($resolved->isNotEmpty())
    <nav aria-label="breadcrumb">
        <ol class="flex flex-wrap items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
            @foreach ($resolved as $i => $item)
                <li class="flex items-center gap-1">
                    @if (! empty($item['route']) && ! $loop->last)
                        <a href="{{ route($item['route']) }}" class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 hover:bg-gray-100 dark:hover:bg-white/[0.05] hover:text-gray-700 dark:hover:text-gray-200 transition">
                            @if (! empty($item['icon']))
                                <x-admin.icon :name="$item['icon']" class="h-3.5 w-3.5" />
                            @endif
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 font-medium text-gray-700 dark:text-brand-400">
                            {{ $item['label'] }}
                        </span>
                    @endif

                    @unless ($loop->last)
                        <x-admin.icon name="chevron-right" class="h-3 w-3 text-gray-300 dark:text-gray-600" />
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
