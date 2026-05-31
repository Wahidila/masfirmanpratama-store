@props([
    'title' => null,
    'padded' => true,
])

<section {{ $attributes->class(['rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]']) }}>
    @if ($title || isset($header))
        <header class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h3>
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
        <footer class="border-t border-gray-100 px-5 py-3 text-theme-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
            {{ $footer }}
        </footer>
    @endisset
</section>
