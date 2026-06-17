@extends('layouts.dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Notifikasi</h1>
    </div>
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Tandai semua dibaca</button>
    </form>
</div>

<div class="space-y-2">
    @forelse($notifications as $notification)
    <div class="bg-white rounded-xl border border-slate-100 p-4 flex items-start gap-3 {{ $notification->isRead() ? 'opacity-60' : '' }}">
        <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0 {{ $notification->isRead() ? 'bg-slate-300' : 'bg-primary-500' }}"></div>
        <div class="flex-1">
            <p class="text-sm font-medium text-slate-800">{{ $notification->title }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $notification->message }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        @if(!$notification->isRead())
        <form method="POST" action="{{ route('notifications.read', $notification) }}">
            @csrf
            <button type="submit" class="text-xs text-primary-600 hover:text-primary-700">Baca</button>
        </form>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-slate-400">
        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
        <p>Belum ada notifikasi</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-6">{{ $notifications->links() }}</div>
@endif
@endsection
