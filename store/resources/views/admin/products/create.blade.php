@extends('layouts.admin', ['active' => 'products'])

@section('title', 'Tambah Produk')

@section('content')
    <x-admin.page-header
        title="Tambah Produk Baru"
        subtitle="Bikin produk untuk katalog store. Status default-nya draft — switch ke active kalau siap tayang.">
        <x-slot name="actions">
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 transition dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/[0.06]">
                ← Kembali ke daftar
            </a>
        </x-slot>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-6">
            <x-admin.alert tone="error" title="Form belum valid">
                Periksa kembali field di bawah — ada kolom yang perlu diperbaiki.
            </x-admin.alert>
        </div>
    @endif

    @include('admin.products._form', ['product' => $product, 'mode' => 'create'])
@endsection
