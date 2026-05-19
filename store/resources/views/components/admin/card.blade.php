@props([
    'title' => null,
    'padded' => true,
])

<section {{ $attributes->class(['rounded-2xl border border-slate-100 bg-white shadow-sm']) }}>
    @if ($title || isset($header))
        <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5">
            <div>
                @if ($title)
                    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
                @endif
                @isset($header)
                    {{ $header }}
                @endisset
            </div>
            @isset($actions)
                <div class="flex items-center gap-2 text-xs">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="{{ $padded ? 'p-5' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="border-t border-slate-100 px-5 py-3 text-xs text-slate-500">
            {{ $footer }}
        </footer>
    @endisset
</section>
