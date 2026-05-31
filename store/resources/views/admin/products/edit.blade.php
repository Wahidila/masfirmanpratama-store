@extends('layouts.admin', ['active' => 'products'])

@section('title', 'Edit Produk · ' . $product->title)

@section('content')
    <x-admin.page-header
        title="Edit Produk"
        :subtitle="'Edit data produk: ' . $product->title">
        <x-slot name="actions">
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 transition dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/[0.06]">
                ← Kembali ke daftar
            </a>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                onsubmit="return confirm('Hapus produk &quot;{{ $product->title }}&quot;? Bisa di-restore dari arsip.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition dark:border-rose-500/40 dark:bg-white/[0.03] dark:text-rose-400 dark:hover:bg-rose-500/10">
                    <x-admin.icon name="trash" class="h-3.5 w-3.5" />
                    Hapus
                </button>
            </form>
        </x-slot>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-6">
            <x-admin.alert tone="error" title="Form belum valid">
                Periksa kembali field di bawah — ada kolom yang perlu diperbaiki.
            </x-admin.alert>
        </div>
    @endif

    @include('admin.products._form', ['product' => $product, 'mode' => 'edit'])
@endsection
