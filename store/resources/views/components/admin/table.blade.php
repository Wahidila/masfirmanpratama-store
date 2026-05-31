@props([
    'columns' => [],
    'empty' => 'Tidak ada data.',
    'rows' => null,
])

<div {{ $attributes->class(['overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-theme-sm']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm">
            @if (! empty($columns))
                <thead class="bg-gray-50 dark:bg-white/[0.02] text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <tr>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 font-medium {{ $col['align'] ?? '' }}">
                                {{ is_array($col) ? ($col['label'] ?? '') : $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                @if ($rows && (is_iterable($rows) ? iterator_count((function () use ($rows) { yield from $rows; })()) === 0 : false))
                    {{-- defensive empty case (rare with Eloquent collections that re-iterate) --}}
                @endif

                @if ($rows !== null && (is_countable($rows) ? count($rows) === 0 : false))
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                            {{ $empty }}
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>
</div>
