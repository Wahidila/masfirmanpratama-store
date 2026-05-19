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
        <ol class="flex flex-wrap items-center gap-1 text-xs text-slate-500">
            @foreach ($resolved as $i => $item)
                <li class="flex items-center gap-1">
                    @if (! empty($item['route']) && ! $loop->last)
                        <a href="{{ route($item['route']) }}" class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 hover:bg-slate-100 hover:text-slate-700 transition">
                            @if (! empty($item['icon']))
                                <x-admin.icon :name="$item['icon']" class="h-3.5 w-3.5" />
                            @endif
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 font-medium text-slate-700">
                            {{ $item['label'] }}
                        </span>
                    @endif

                    @unless ($loop->last)
                        <x-admin.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
