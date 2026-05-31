@extends('layouts.admin', ['active' => 'settings'])

@section('title', 'Settings · Admin')

@section('content')
    <x-admin.page-header
        title="Settings"
        subtitle="Kelola info toko & rekening bank yang dipakai di halaman publik (checkout, upload, kontak)." />

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6 flex items-center gap-2 text-xs font-medium border-b border-slate-200">
        <a href="{{ route('admin.settings.index', ['tab' => 'store-info']) }}"
            class="px-4 py-2.5 -mb-px border-b-2 transition {{ $tab === 'store-info' ? 'border-primary-600 text-primary-700' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
            Store Info
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'bank-accounts']) }}"
            class="px-4 py-2.5 -mb-px border-b-2 transition {{ $tab === 'bank-accounts' ? 'border-primary-600 text-primary-700' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
            Bank Accounts
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'shipping']) }}"
            class="px-4 py-2.5 -mb-px border-b-2 transition {{ $tab === 'shipping' ? 'border-primary-600 text-primary-700' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
            Shipping
        </a>
    </div>

    @if ($tab === 'store-info')
        @include('admin.settings._store_info', ['storeInfo' => $storeInfo])
    @elseif ($tab === 'shipping')
        @include('admin.settings._shipping', [
            'shippingData' => $shippingData,
            'availableCouriers' => $availableCouriers,
        ])
    @else
        @include('admin.settings._bank_accounts', ['bankAccounts' => $bankAccounts])
    @endif
@endsection
