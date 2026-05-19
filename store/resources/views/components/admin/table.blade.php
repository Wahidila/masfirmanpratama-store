@props([
    'columns' => [],
    'empty' => 'Tidak ada data.',
    'rows' => null,
])

<div {{ $attributes->class(['overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            @if (! empty($columns))
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 font-medium {{ $col['align'] ?? '' }}">
                                {{ is_array($col) ? ($col['label'] ?? '') : $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-slate-100 text-slate-700">
                @if ($rows && (is_iterable($rows) ? iterator_count((function () use ($rows) { yield from $rows; })()) === 0 : false))
                    {{-- defensive empty case (rare with Eloquent collections that re-iterate) --}}
                @endif

                @if ($rows !== null && (is_countable($rows) ? count($rows) === 0 : false))
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="px-4 py-8 text-center text-sm text-slate-400">
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
