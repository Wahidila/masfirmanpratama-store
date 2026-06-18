@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Materi Marketing</h1>
    <p class="text-slate-500 mt-1">Download materi promosi untuk membantu penjualan Anda</p>
</div>

{{-- Filter --}}
<div class="mb-4">
    <form method="GET" class="flex items-center gap-2">
        <select name="type" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Gambar</option>
            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
            <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Dokumen</option>
            <option value="template" {{ request('type') === 'template' ? 'selected' : '' }}>Template</option>
        </select>
    </form>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($materials as $material)
    <div class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-md transition {{ !$material->accessible ? 'opacity-60' : '' }}">
        <div class="flex items-start gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $material->type === 'image' ? 'bg-blue-50' : '' }}
                {{ $material->type === 'video' ? 'bg-purple-50' : '' }}
                {{ $material->type === 'document' ? 'bg-accent-50' : '' }}
                {{ $material->type === 'template' ? 'bg-secondary-50' : '' }}">
                <i data-lucide="{{ $material->type === 'image' ? 'image' : ($material->type === 'video' ? 'video' : ($material->type === 'document' ? 'file-text' : 'layout-template')) }}"
                   class="w-5 h-5 {{ $material->type === 'image' ? 'text-blue-600' : ($material->type === 'video' ? 'text-purple-600' : ($material->type === 'document' ? 'text-accent-600' : 'text-secondary-600')) }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-slate-800 truncate">{{ $material->title }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ strtoupper($material->type) }} · {{ number_format($material->file_size / 1024 / 1024, 1) }} MB</p>
            </div>
        </div>
        @if($material->description)
        <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $material->description }}</p>
        @endif
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-400">{{ $material->download_count }} download</span>
            @if($material->accessible)
            <a href="{{ route('materials.download', $material) }}" class="text-xs px-3 py-1.5 bg-primary-50 text-primary-600 rounded-lg font-medium hover:bg-primary-100 transition">
                Download
            </a>
            @else
            <span class="text-xs px-3 py-1.5 bg-slate-50 text-slate-400 rounded-lg font-medium">Terkunci</span>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-slate-400">
        <i data-lucide="folder-open" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
        <p>Belum ada materi marketing tersedia</p>
    </div>
    @endforelse
</div>

@if($materials->hasPages())
<div class="mt-6">{{ $materials->links() }}</div>
@endif
@endsection
