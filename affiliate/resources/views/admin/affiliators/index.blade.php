@extends('admin.layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Kelola Affiliator</h1>
</div>

<div class="bg-white rounded-2xl border border-slate-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..."
               class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 outline-none w-64">
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-xl hover:bg-primary-700">Cari</button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Nama</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Email</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Tipe</th>
                <th class="text-center px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="text-right px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($affiliators as $aff)
            <tr class="hover:bg-slate-50/50">
                <td class="px-4 py-3 font-medium text-slate-700">{{ $aff->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $aff->email }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $aff->type->name }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $aff->status === 'active' ? 'bg-secondary-50 text-secondary-700' : '' }}
                        {{ $aff->status === 'pending' ? 'bg-accent-50 text-accent-700' : '' }}
                        {{ $aff->status === 'suspended' ? 'bg-rose-50 text-rose-700' : '' }}">
                        {{ ucfirst($aff->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.affiliators.show', $aff) }}" class="text-xs px-2 py-1 bg-slate-100 rounded-lg hover:bg-slate-200">Detail</a>
                        @if($aff->status === 'pending')
                        <form method="POST" action="{{ route('admin.affiliators.approve', $aff) }}" class="inline">
                            @csrf
                            <button class="text-xs px-2 py-1 bg-secondary-50 text-secondary-700 rounded-lg hover:bg-secondary-100">Approve</button>
                        </form>
                        @elseif($aff->status === 'active')
                        <form method="POST" action="{{ route('admin.affiliators.suspend', $aff) }}" class="inline">
                            @csrf
                            <button class="text-xs px-2 py-1 bg-rose-50 text-rose-700 rounded-lg hover:bg-rose-100">Suspend</button>
                        </form>
                        @elseif($aff->status === 'suspended')
                        <form method="POST" action="{{ route('admin.affiliators.reactivate', $aff) }}" class="inline">
                            @csrf
                            <button class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">Reactivate</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada affiliator</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($affiliators->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $affiliators->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
