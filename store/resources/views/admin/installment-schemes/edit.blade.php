@extends('layouts.admin', ['active' => 'installments'])

@section('title', 'Edit Skema · Admin')

@section('content')
    <x-admin.page-header
        :title="'Edit Skema: ' . $scheme->name"
        subtitle="Ubah skema. Perubahan langsung tercermin di dropdown checkout.">
        <x-slot:actions>
            <a href="{{ route('admin.installment-schemes.index') }}"
               class="inline-flex items-center rounded-xl border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                ← Kembali
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @include('admin.installment-schemes._form', [
        'scheme' => $scheme,
        'products' => $products,
        'action' => route('admin.installment-schemes.update', $scheme),
        'method' => 'PUT',
    ])
@endsection
