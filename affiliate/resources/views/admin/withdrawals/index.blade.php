@extends('admin.layouts.admin')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-6">Kelola Penarikan</h1>

<div class="mb-4">
    <form method="GET" class="flex items-center gap-2">
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Affiliator</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Metode</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Rekening</th>
                <th class="text-right px-4 py-3 font-medium text-slate-600">Jumlah</th>
                <th class="text-center px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="text-right px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($withdrawals as $wd)
            <tr>
                <td class="px-4 py-3 text-slate-700">{{ $wd->affiliator->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $wd->method->name }}</td>
                <td class="px-4 py-3 text-slate-600 text-xs">{{ $wd->account_name }}<br>{{ $wd->account_number }}</td>
                <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($wd->amount, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $wd->status === 'completed' ? 'bg-secondary-50 text-secondary-700' : '' }}
                        {{ $wd->status === 'pending' ? 'bg-accent-50 text-accent-700' : '' }}
                        {{ $wd->status === 'rejected' ? 'bg-rose-50 text-rose-700' : '' }}">
                        {{ ucfirst($wd->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    @if($wd->status === 'pending')
                    <div class="flex items-center justify-end gap-1">
                        <form method="POST" action="{{ route('admin.withdrawals.approve', $wd) }}" class="inline">@csrf
                            <button class="text-xs px-2 py-1 bg-secondary-50 text-secondary-700 rounded-lg">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.withdrawals.reject', $wd) }}" class="inline"
                              x-data @submit.prevent="if(confirm('Alasan reject?')) { $el.querySelector('[name=admin_note]').value = prompt('Alasan:'); $el.submit(); }">
                            @csrf
                            <input type="hidden" name="admin_note" value="">
                            <button class="text-xs px-2 py-1 bg-rose-50 text-rose-700 rounded-lg">Reject</button>
                        </form>
                    </div>
                    @else
                    <span class="text-xs text-slate-400">{{ $wd->processed_at ? $wd->processed_at->format('d/m/Y') : '-' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada penarikan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($withdrawals->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $withdrawals->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
