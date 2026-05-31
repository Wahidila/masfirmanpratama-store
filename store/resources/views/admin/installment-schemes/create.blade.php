@extends('layouts.admin', ['active' => 'installments'])

@section('title', 'Skema Baru · Admin')

@section('content')
    <x-admin.page-header
        title="Skema Cicilan Baru"
        subtitle="Tambah skema pembayaran yang akan tampil di dropdown checkout.">
        <x-slot:actions>
            <x-admin.button href="{{ route('admin.installment-schemes.index') }}" variant="outline" size="sm">
                ← Kembali
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @include('admin.installment-schemes._form', [
        'scheme' => $scheme,
        'products' => $products,
        'action' => route('admin.installment-schemes.store'),
        'method' => 'POST',
    ])
@endsection
