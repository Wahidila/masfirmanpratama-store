@extends('layouts.affiliate-dashboard')

@section('title', 'Link Referral')
@section('page-title', 'Link Referral')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Kode Referral Anda</h2>
                <p class="text-sm text-slate-500 mt-0.5">Bagikan link referral untuk mendapatkan komisi dari setiap pembelian.</p>
            </div>
            <form method="POST" action="{{ route('affiliate.referral-links.store') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-full hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Kode Baru
                </button>
            </form>
        </div>

        @if ($codes->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="link" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500">Belum ada kode referral. Klik tombol di atas untuk membuat.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($codes as $code)
                    <div x-data="{ copied: false }" class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-sm font-semibold text-slate-800">{{ $code->code }}</span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-500">{{ number_format($code->clicks_count, 0, ',', '.') }} klik</span>
                            </div>
                            <code class="text-xs text-slate-500 break-all">{{ $baseUrl . $code->code }}</code>
                        </div>
                        <button
                            type="button"
                            @click="navigator.clipboard.writeText('{{ $baseUrl . $code->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="copied ? 'bg-secondary-50 text-secondary-700' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'"
                        >
                            <i data-lucide="copy" class="w-4 h-4" x-show="!copied"></i>
                            <i data-lucide="check" class="w-4 h-4" x-show="copied" x-cloak></i>
                            <span x-text="copied ? 'Tersalin!' : 'Salin Link'"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
