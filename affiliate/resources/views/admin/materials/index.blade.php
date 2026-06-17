@extends('admin.layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Materi Marketing</h1>
    <a href="{{ route('admin.materials.create') }}" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-xl hover:bg-primary-700">+ Upload Materi</a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Judul</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Tipe</th>
                <th class="text-center px-4 py-3 font-medium text-slate-600">Download</th>
                <th class="text-center px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="text-right px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($materials as $m)
            <tr>
                <td class="px-4 py-3 text-slate-700 font-medium">{{ $m->title }}</td>
                <td class="px-4 py-3 text-slate-600">{{ ucfirst($m->type) }}</td>
                <td class="px-4 py-3 text-center">{{ $m->download_count }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full {{ $m->is_active ? 'bg-secondary-50 text-secondary-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <form method="POST" action="{{ route('admin.materials.toggle', $m) }}" class="inline">@csrf
                            <button class="text-xs px-2 py-1 bg-slate-100 rounded-lg">{{ $m->is_active ? 'Off' : 'On' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.materials.destroy', $m) }}" class="inline" onsubmit="return confirm('Hapus materi?')">
                            @csrf @method('DELETE')
                            <button class="text-xs px-2 py-1 bg-rose-50 text-rose-700 rounded-lg">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada materi</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($materials->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $materials->links() }}</div>
    @endif
</div>
@endsection
