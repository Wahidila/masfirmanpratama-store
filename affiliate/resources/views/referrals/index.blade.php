@extends('layouts.dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Link Referral</h1>
        <p class="text-slate-500 mt-1">Kelola link referral Anda untuk promosi produk</p>
    </div>
    <a href="{{ route('referrals.create') }}" class="px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
        + Buat Link Baru
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Kode</th>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Label</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Klik</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Order</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Status</th>
                    <th class="text-right px-6 py-3 font-medium text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($referrals as $referral)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4">
                        <div x-data="{ copied: false }" class="flex items-center gap-2">
                            <code class="text-xs bg-slate-100 px-2 py-1 rounded font-mono">{{ $referral->code }}</code>
                            <button @click="navigator.clipboard.writeText('{{ url('/ref/' . $referral->code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="text-slate-400 hover:text-primary-600">
                                <i x-show="!copied" data-lucide="copy" class="w-4 h-4"></i>
                                <i x-show="copied" x-cloak data-lucide="check" class="w-4 h-4 text-secondary-500"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $referral->label ?: '-' }}</td>
                    <td class="px-6 py-4 text-center text-slate-700 font-medium">{{ number_format($referral->clicks_count) }}</td>
                    <td class="px-6 py-4 text-center text-slate-700 font-medium">{{ number_format($referral->orders_count) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $referral->is_active ? 'bg-secondary-50 text-secondary-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $referral->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('referrals.edit', $referral) }}" class="text-slate-400 hover:text-primary-600">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form method="POST" action="{{ route('referrals.toggle', $referral) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-accent-600">
                                    <i data-lucide="{{ $referral->is_active ? 'pause' : 'play' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('referrals.destroy', $referral) }}" class="inline"
                                  onsubmit="return confirm('Yakin hapus link referral ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-600">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <i data-lucide="link" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p>Belum ada link referral. Buat link pertama Anda!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($referrals->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $referrals->links() }}
    </div>
    @endif
</div>
@endsection
