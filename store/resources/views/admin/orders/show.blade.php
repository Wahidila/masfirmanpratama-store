@extends('layouts.admin', ['active' => 'orders'])

@section('title', 'Pesanan ' . $order->order_number . ' · Admin')

@php
    $statusLabel = [
        'pending' => 'Pending',
        'partial_paid' => 'Cicilan',
        'paid' => 'Lunas',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Batal',
        'refunded' => 'Refund',
    ];
    $paymentToneMap = [
        'pending' => 'amber',
        'verified' => 'secondary',
        'rejected' => 'slate',
    ];
    $paymentLabel = [
        'pending' => 'Menunggu',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];
@endphp

@section('content')
    <x-admin.page-header
        :title="'Pesanan ' . $order->order_number"
        :subtitle="'Dibuat ' . $order->created_at?->format('d M Y · H:i') . ' WIB'">
        <x-slot:actions>
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center rounded-xl border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                ← Kembali
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6">
            <x-admin.alert tone="danger" dismissible>{{ session('error') }}</x-admin.alert>
        </div>
    @endif

    @if (session('info'))
        <div class="mb-6">
            <x-admin.alert tone="primary" dismissible>{{ session('info') }}</x-admin.alert>
        </div>
    @endif

    {{-- Status & total summary strip --}}
    <section class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.card>
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</div>
            <div class="mt-2">
                <x-admin.status-badge :status="$order->status" class="!text-sm !px-3 !py-1" />
            </div>
        </x-admin.card>
        <x-admin.card>
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Pesanan</div>
            <div class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Rp {{ number_format((float) $order->total, 0, ',', '.') }}
            </div>
        </x-admin.card>
        <x-admin.card>
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sudah Lunas</div>
            <div class="mt-2 text-2xl font-semibold text-secondary-700 dark:text-secondary-400">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </div>
            @if ($totalPending > 0)
                <div class="mt-1 text-xs text-accent-600 dark:text-accent-400">
                    + Rp {{ number_format($totalPending, 0, ',', '.') }} menunggu verifikasi
                </div>
            @endif
        </x-admin.card>
        <x-admin.card>
            <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sisa</div>
            <div class="mt-2 text-2xl font-semibold {{ $remaining > 0 ? 'text-accent-700 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500' }}">
                Rp {{ number_format($remaining, 0, ',', '.') }}
            </div>
        </x-admin.card>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left col: items + payments --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Items --}}
            <x-admin.card :padded="false">
                <div class="border-b border-gray-100 dark:border-gray-800 px-5 py-3">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Item Pesanan</h2>
                </div>
                @if ($order->items->isEmpty())
                    <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada item di pesanan ini.
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                        <thead class="bg-gray-50/60 dark:bg-white/[0.03] text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left font-medium">Produk</th>
                                <th class="px-5 py-2 text-right font-medium">Qty</th>
                                <th class="px-5 py-2 text-right font-medium">Harga</th>
                                <th class="px-5 py-2 text-right font-medium">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-800 dark:text-white/90">
                                            {{ $item->product?->title ?? '(produk dihapus)' }}
                                        </div>
                                        @if ($item->product?->slug)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $item->product->slug }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">{{ $item->qty }}</td>
                                    <td class="px-5 py-3 text-right">
                                        Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-medium">
                                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/60 dark:bg-white/[0.03] text-sm">
                            <tr>
                                <td colspan="3" class="px-5 py-3 text-right text-gray-500 dark:text-gray-400">Total</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-800 dark:text-white/90">
                                    Rp {{ number_format((float) $order->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </x-admin.card>

            {{-- Payments timeline --}}
            <x-admin.card :padded="false">
                <div class="border-b border-gray-100 dark:border-gray-800 px-5 py-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pembayaran</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->payments->count() }} entri</span>
                </div>
                @if ($order->payments->isEmpty())
                    <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada bukti bayar yang di-upload customer.
                    </div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($order->payments as $payment)
                            @php
                                $pTone = $paymentToneMap[$payment->status] ?? 'slate';
                                $pToneClass = match ($pTone) {
                                    'amber' => 'bg-accent-50 text-accent-800 ring-accent-200 dark:bg-warning-500/15 dark:text-warning-300 dark:ring-warning-500/40',
                                    'secondary' => 'bg-secondary-50 text-secondary-800 ring-secondary-200 dark:bg-secondary-500/15 dark:text-secondary-400 dark:ring-secondary-500/40',
                                    default => 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-gray-600',
                                };
                            @endphp
                            <li class="px-5 py-4" x-data="{ showApprove: false, showReject: false }">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-semibold text-gray-800 dark:text-white/90">
                                                Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                            </span>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $pToneClass }}">
                                                {{ $paymentLabel[$payment->status] ?? $payment->status }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Metode: <span class="text-gray-700 dark:text-gray-300 font-medium">{{ ucfirst($payment->method) }}</span>
                                            @if ($payment->paid_at)
                                                · Dibayar {{ $payment->paid_at->format('d M Y H:i') }}
                                            @endif
                                        </div>
                                        @if ($payment->verified_at)
                                            <div class="mt-1 text-xs {{ $payment->status === 'verified' ? 'text-secondary-700 dark:text-secondary-400' : 'text-gray-500 dark:text-gray-400' }}">
                                                {{ $payment->status === 'verified' ? 'Diverifikasi' : 'Diproses' }}
                                                {{ $payment->verified_at->format('d M Y H:i') }}
                                                @if ($payment->verifier)
                                                    oleh {{ $payment->verifier->name }}
                                                @endif
                                            </div>
                                        @endif
                                        @if ($payment->status === 'rejected' && $payment->rejection_reason)
                                            <div class="mt-2 rounded-lg bg-gray-50 border border-gray-100 px-3 py-2 text-xs text-gray-700 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-300">
                                                <div class="font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-0.5">Alasan tolak</div>
                                                <div>{{ $payment->rejection_reason }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($payment->proof_path)
                                        <div class="text-xs">
                                            <span class="text-gray-500 dark:text-gray-400">Bukti:</span>
                                            <span class="font-mono text-gray-700 dark:text-gray-300">{{ basename($payment->proof_path) }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($payment->status === 'pending')
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button type="button"
                                                @click="showApprove = !showApprove; showReject = false"
                                                class="inline-flex items-center rounded-lg bg-secondary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-secondary-500 transition">
                                            ✓ Approve
                                        </button>
                                        <button type="button"
                                                @click="showReject = !showReject; showApprove = false"
                                                class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                                            ✗ Reject
                                        </button>
                                    </div>

                                    {{-- Approve form --}}
                                    <form x-show="showApprove" x-cloak
                                          method="POST"
                                          action="{{ route('admin.orders.payments.approve', [$order, $payment]) }}"
                                          class="mt-3 rounded-xl border border-secondary-100 bg-secondary-50/50 p-4 space-y-3 dark:border-secondary-500/30 dark:bg-secondary-500/10">
                                        @csrf
                                        <div>
                                            <label for="approve-amount-{{ $payment->id }}" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Nominal terverifikasi
                                            </label>
                                            <input id="approve-amount-{{ $payment->id }}"
                                                   type="number"
                                                   name="amount"
                                                   step="0.01"
                                                   min="0"
                                                   value="{{ $payment->amount }}"
                                                   class="block w-full rounded-lg border-gray-200 text-sm focus:border-secondary-500 focus:ring-secondary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Edit kalau jumlah aktual transfer beda dari yang diinput customer.
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="inline-flex items-center rounded-lg bg-secondary-600 px-4 py-2 text-sm font-medium text-white hover:bg-secondary-500 transition">
                                                Konfirmasi Approve
                                            </button>
                                            <button type="button" @click="showApprove = false"
                                                    class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                Batal
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Reject form --}}
                                    <form x-show="showReject" x-cloak
                                          method="POST"
                                          action="{{ route('admin.orders.payments.reject', [$order, $payment]) }}"
                                          class="mt-3 rounded-xl border border-gray-200 bg-gray-50/50 p-4 space-y-3 dark:border-gray-700 dark:bg-white/[0.03]">
                                        @csrf
                                        <div>
                                            <label for="reject-reason-{{ $payment->id }}" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Alasan tolak <span class="text-rose-500">*</span>
                                            </label>
                                            <textarea id="reject-reason-{{ $payment->id }}"
                                                      name="reason"
                                                      rows="3"
                                                      required
                                                      minlength="3"
                                                      maxlength="500"
                                                      class="block w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                                                      placeholder="Mis. nominal tidak sesuai, bukti tidak jelas, transfer ke rekening yang salah..."></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-500 transition">
                                                Konfirmasi Reject
                                            </button>
                                            <button type="button" @click="showReject = false"
                                                    class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>
        </div>

        {{-- Right col: customer + shipping --}}
        <div class="space-y-6">
            <x-admin.card>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Customer</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama</dt>
                        <dd class="mt-0.5 font-medium text-gray-800 dark:text-white/90">{{ $order->customer_name }}</dd>
                    </div>
                    @if ($order->phone)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Telepon / WA</dt>
                            <dd class="mt-0.5 text-gray-700 dark:text-gray-300">{{ $order->phone }}</dd>
                        </div>
                    @endif
                    @if ($order->email)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-0.5 text-gray-700 dark:text-gray-300 break-all">{{ $order->email }}</dd>
                        </div>
                    @endif
                </dl>
            </x-admin.card>

            <x-admin.card>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Pengiriman</h2>
                @if ($order->address)
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $order->address }}</p>
                @else
                    <p class="text-sm italic text-gray-500 dark:text-gray-400">Alamat belum diisi.</p>
                @endif
                @if ($order->ref_code)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kode Referral</dt>
                        <dd class="mt-0.5 font-mono text-gray-700 dark:text-gray-300">{{ $order->ref_code }}</dd>
                    </div>
                @endif
            </x-admin.card>

            <x-admin.card>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Aksi Pengiriman</h2>

                @php
                    $fulfillmentToneMap = [
                        'shipped' => 'secondary',
                        'waiting_awb' => 'amber',
                        'pending_payment' => 'amber',
                        'failed' => 'slate',
                    ];
                    $fulfillmentLabel = [
                        'shipped' => 'Terkirim',
                        'waiting_awb' => 'Tunggu AWB',
                        'pending_payment' => 'Bayar Ongkir',
                        'failed' => 'Gagal',
                    ];
                @endphp

                {{-- Fulfillment info (tampil di semua status kalau fulfillment_status terisi) --}}
                @if ($order->fulfillment_status)
                    <div class="mb-4 space-y-2 text-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status Fulfillment:</span>
                            @php
                                $fTone = $fulfillmentToneMap[$order->fulfillment_status] ?? 'slate';
                                $fToneClass = match ($fTone) {
                                    'amber' => 'bg-accent-50 text-accent-800 ring-accent-200 dark:bg-warning-500/15 dark:text-warning-300 dark:ring-warning-500/40',
                                    'secondary' => 'bg-secondary-50 text-secondary-800 ring-secondary-200 dark:bg-secondary-500/15 dark:text-secondary-400 dark:ring-secondary-500/40',
                                    default => 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $fToneClass }}">
                                {{ $fulfillmentLabel[$order->fulfillment_status] ?? $order->fulfillment_status }}
                            </span>
                        </div>
                        @if ($order->tracking_status)
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tracking:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-300">{{ $order->tracking_status }}</span>
                            </div>
                        @endif
                        @if ($order->shipping_resi)
                            <div>
                                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Resi:</span>
                                <span class="ml-1 font-mono text-gray-800 dark:text-gray-200 break-all">{{ $order->shipping_resi }}</span>
                            </div>
                        @endif
                        @if ($order->label_url)
                            <div>
                                <a href="{{ $order->label_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-700 underline">
                                    <i data-lucide="external-link" class="h-3 w-3"></i>
                                    Label Pengiriman
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($order->status === 'shipped' || $order->status === 'completed')
                    {{-- Order sudah dikirim — tampilkan info resi read-only --}}
                    <div class="space-y-3 text-sm">
                        <div class="rounded-xl bg-secondary-50 border border-secondary-200 p-3 dark:bg-secondary-500/10 dark:border-secondary-500/30">
                            <div class="flex items-center gap-2 text-secondary-800 dark:text-secondary-400 font-semibold">
                                <i data-lucide="truck" class="h-4 w-4"></i>
                                <span>Sudah Dikirim</span>
                            </div>
                            @if ($order->shipped_at)
                                <p class="mt-1 text-xs text-secondary-700 dark:text-secondary-400">
                                    {{ \Illuminate\Support\Carbon::parse($order->shipped_at)->translatedFormat('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                        <dl class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kurir</dt>
                                <dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">{{ $order->shipping_courier ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nomor Resi</dt>
                                <dd class="mt-0.5 font-mono text-gray-800 dark:text-gray-200 break-all">{{ $order->shipping_resi ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                @elseif ($canShip)
                    @if ($order->shipping_courier && $order->shipping_service)
                        {{-- Generate Resi Otomatis --}}
                        <div class="mb-4 rounded-xl border border-primary-200 bg-primary-50/50 p-4 dark:border-brand-500/30 dark:bg-brand-500/10">
                            <h3 class="text-sm font-semibold text-primary-800 dark:text-brand-400 mb-2">
                                <i data-lucide="wand" class="h-4 w-4 inline mr-1"></i>
                                Generate Resi Otomatis
                            </h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                Buat resi pengiriman otomatis via kurir
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $order->shipping_courier }}</span>
                                — {{ $order->shipping_service }}.
                            </p>
                            <form
                                method="POST"
                                action="{{ route('admin.orders.generate-shipment', $order) }}"
                            >
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1">
                                    <i data-lucide="truck" class="h-4 w-4"></i>
                                    Generate Resi Otomatis
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Order siap kirim (status=paid) — tampilkan form input resi manual --}}
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        Atau isi kurir &amp; nomor resi manual untuk transition ke
                        <span class="font-medium text-gray-700 dark:text-gray-300">Dikirim</span>.
                    </p>
                    <form
                        method="POST"
                        action="{{ route('admin.orders.ship', $order) }}"
                        class="space-y-3"
                        data-testid="form-input-resi"
                    >
                        @csrf
                        <div>
                            <label for="shipping_courier" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                Kurir <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="shipping_courier"
                                id="shipping_courier"
                                required
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 @error('shipping_courier') border-rose-400 @enderror"
                            >
                                <option value="">— Pilih kurir —</option>
                                @foreach ($couriers as $c)
                                    <option value="{{ $c }}" @selected(old('shipping_courier') === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('shipping_courier')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_resi" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                Nomor Resi / AWB <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="shipping_resi"
                                id="shipping_resi"
                                value="{{ old('shipping_resi') }}"
                                required
                                minlength="4"
                                maxlength="64"
                                placeholder="cth. JNE1234567890"
                                class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500 @error('shipping_resi') border-rose-400 @enderror"
                            >
                            @error('shipping_resi')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                            onclick="return confirm('Konfirmasi: tandai order ini sebagai dikirim dengan resi yang diinput?');"
                        >
                            <i data-lucide="truck" class="h-4 w-4"></i>
                            Tandai Dikirim
                        </button>
                    </form>
                @else
                    {{-- Status belum siap kirim — info kondisi --}}
                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-3 text-xs text-gray-600 dark:bg-white/[0.03] dark:border-gray-700 dark:text-gray-400">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="info" class="h-4 w-4 text-gray-500 dark:text-gray-400"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Belum siap kirim</span>
                        </div>
                        <p>
                            Status sekarang: <span class="font-mono font-medium">{{ $order->status }}</span>.
                            Form input resi tersedia setelah pembayaran lunas terverifikasi (status =
                            <span class="font-mono">paid</span>).
                        </p>
                    </div>
                @endif
            </x-admin.card>
        </div>
    </div>
@endsection
