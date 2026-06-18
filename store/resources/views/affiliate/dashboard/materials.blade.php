@extends('layouts.affiliate-dashboard')

@section('title', 'Materi Marketing')
@section('page-title', 'Materi Marketing')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Materi Marketing</h2>
            <p class="text-sm text-slate-500 mt-0.5">Download materi promosi untuk mendukung aktivitas affiliate Anda.</p>
        </div>

        @if ($materials->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="file-text" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500">Belum ada materi tersedia.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($materials as $material)
                    <div class="border border-slate-200 rounded-xl p-4 hover:border-primary-200 hover:shadow-sm transition-all">
                        <div class="flex items-start gap-3">
                            <span class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-slate-900 truncate">{{ $material->title }}</h3>
                                @if ($material->description)
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $material->description }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($material->file_path)
                            <a
                                href="{{ asset('storage/' . $material->file_path) }}"
                                target="_blank"
                                class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-800 transition-colors"
                            >
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                Download
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
