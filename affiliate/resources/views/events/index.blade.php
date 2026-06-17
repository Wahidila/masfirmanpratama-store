@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Event & Gamifikasi</h1>
    <p class="text-slate-500 mt-1">Ikuti event dan dapatkan reward tambahan</p>
</div>

<div class="grid sm:grid-cols-2 gap-4">
    @forelse($events as $event)
    <div class="bg-white rounded-2xl border border-slate-100 p-6 hover:shadow-md transition">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $event->type === 'challenge' ? 'bg-primary-50 text-primary-700' : '' }}
                {{ $event->type === 'contest' ? 'bg-accent-50 text-accent-700' : '' }}
                {{ $event->type === 'bonus' ? 'bg-secondary-50 text-secondary-700' : '' }}">
                {{ ucfirst($event->type) }}
            </span>
            @if($event->isActive())
            <span class="text-xs px-2.5 py-1 rounded-full bg-green-50 text-green-700 font-medium">Berlangsung</span>
            @endif
        </div>
        <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ $event->title }}</h3>
        <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $event->description }}</p>
        <div class="flex items-center justify-between text-xs text-slate-400 mb-4">
            <span>{{ $event->start_date->format('d M') }} — {{ $event->end_date->format('d M Y') }}</span>
            <span>{{ $event->participants()->count() }} peserta</span>
        </div>
        <a href="{{ route('events.show', $event) }}" class="block text-center py-2 border border-primary-200 text-primary-600 font-medium rounded-xl hover:bg-primary-50 text-sm transition">
            Lihat Detail
        </a>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-slate-400">
        <i data-lucide="trophy" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
        <p>Belum ada event aktif saat ini</p>
    </div>
    @endforelse
</div>

@if($events->hasPages())
<div class="mt-6">{{ $events->links() }}</div>
@endif
@endsection
