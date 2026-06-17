@extends('admin.layouts.admin')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-6">Pengaturan Komisi</h1>

<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <form method="POST" action="{{ route('admin.commissions.settings.update') }}">
        @csrf @method('PUT')
        <table class="w-full text-sm mb-4">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left py-2 font-medium text-slate-600">Tipe</th>
                    <th class="text-left py-2 font-medium text-slate-600">Produk</th>
                    <th class="text-center py-2 font-medium text-slate-600">Rate (%)</th>
                    <th class="text-center py-2 font-medium text-slate-600">Cooling (hari)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($settings as $setting)
                <tr>
                    <td class="py-3">{{ $setting->affiliatorType->name ?? 'Global' }}</td>
                    <td class="py-3">{{ $setting->product_type ?? 'Semua' }}</td>
                    <td class="py-3 text-center">
                        <input type="number" step="0.01" name="settings[{{ $setting->id }}][rate]" value="{{ $setting->rate }}"
                               class="w-20 px-2 py-1 border border-slate-200 rounded-lg text-center text-sm">
                    </td>
                    <td class="py-3 text-center">
                        <input type="number" name="settings[{{ $setting->id }}][cooling_days]" value="{{ $setting->cooling_days }}"
                               class="w-16 px-2 py-1 border border-slate-200 rounded-lg text-center text-sm">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-xl hover:bg-primary-700">Simpan Pengaturan</button>
    </form>
</div>
@endsection
