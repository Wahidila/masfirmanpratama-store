@extends('layouts.affiliate-dashboard')

@section('title', 'Event & Leaderboard')
@section('page-title', 'Event & Leaderboard')

@section('content')
    {{-- Leaderboard --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="trophy" class="w-5 h-5 text-amber-500"></i>
            <h2 class="text-lg font-semibold text-slate-900">Leaderboard</h2>
        </div>

        @if ($leaderboard->isEmpty())
            <p class="text-sm text-slate-500 py-4">Belum ada data leaderboard.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-2 font-medium text-slate-500 w-12">#</th>
                            <th class="text-left py-3 px-2 font-medium text-slate-500">Nama</th>
                            <th class="text-left py-3 px-2 font-medium text-slate-500">Tipe</th>
                            <th class="text-right py-3 px-2 font-medium text-slate-500">Total Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaderboard as $index => $entry)
                            @php
                                $isMe = $entry->affiliator_id === $affiliator->id;
                                $rowClass = $isMe ? 'bg-primary-50' : '';
                                $rankIcon = match($index) {
                                    0 => '🥇',
                                    1 => '🥈',
                                    2 => '🥉',
                                    default => $index + 1,
                                };
                            @endphp
                            <tr class="border-b border-slate-50 {{ $rowClass }}">
                                <td class="py-3 px-2 text-center">{{ $rankIcon }}</td>
                                <td class="py-3 px-2 whitespace-nowrap font-medium text-slate-800">
                                    {{ $entry->affiliator?->name ?? '-' }}
                                    @if ($isMe)
                                        <span class="text-xs text-primary-600 font-normal">(Anda)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 whitespace-nowrap text-slate-600 capitalize">{{ str_replace('_', ' ', $entry->affiliator?->type ?? '-') }}</td>
                                <td class="py-3 px-2 whitespace-nowrap text-right font-medium text-slate-900">Rp {{ number_format((float) $entry->total_komisi, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Event Aktif --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Event Aktif</h2>

        @if ($events->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="calendar" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500">Tidak ada event aktif saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($events as $event)
                    <div class="border border-slate-200 rounded-xl p-4 hover:border-primary-200 hover:shadow-sm transition-all">
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ $event->title }}</h3>
                        @if ($event->description)
                            <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $event->description }}</p>
                        @endif
                        <div class="flex items-center gap-3 text-xs text-slate-400">
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $event->starts_at?->format('d M') }} — {{ $event->ends_at?->format('d M Y') }}
                            </span>
                        </div>
                        @if ($event->reward_note)
                            <div class="mt-2 px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded-md inline-flex items-center gap-1">
                                <i data-lucide="gift" class="w-3 h-3"></i>
                                {{ $event->reward_note }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
