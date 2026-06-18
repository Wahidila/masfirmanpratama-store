<x-layouts.store
    title="Pendaftaran Berhasil — {{ $course->title }}"
    description="Pendaftaran kelas berhasil. Lakukan pembayaran untuk konfirmasi."
>
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white py-8 sm:py-12">
    <div class="mx-auto max-w-2xl px-4 sm:px-6">

        {{-- Success icon --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Pendaftaran Berhasil! 🎉</h1>
            <p class="mt-2 text-slate-500 text-sm">Detail pembayaran sudah dikirim ke WhatsApp kamu.</p>
        </div>

        {{-- Order info --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm space-y-5 mb-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <span class="text-sm text-slate-500">Order ID</span>
                <span class="font-mono font-bold text-sm text-slate-900">{{ $order->order_number }}</span>
            </div>

            <div class="flex items-center gap-4">
                @if ($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}"
                         class="w-14 h-14 rounded-xl object-cover shrink-0">
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-900 text-sm">{{ $course->title }}</h3>
                    @if ($course->subtitle)
                        <p class="text-xs text-slate-500">{{ $course->subtitle }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Nama</span>
                    <span class="font-medium text-slate-900">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">WhatsApp</span>
                    <span class="font-medium text-slate-900">{{ $order->phone }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Email</span>
                    <span class="font-medium text-slate-900">{{ $order->email }}</span>
                </div>
                <hr class="border-slate-200">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Total Pembayaran</span>
                    <span class="font-extrabold text-slate-900">Rp {{ number_format((int) $order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Metode</span>
                    <span class="font-medium text-slate-900">{{ $isCicilan ? 'Cicilan (' . $order->payments->count() . 'x)' : 'Lunas' }}</span>
                </div>
                @if ($isCicilan && $firstPayment)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Bayar Sekarang (DP)</span>
                        <span class="font-bold text-primary-600">Rp {{ number_format((int) $firstPayment->amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Status</span>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                        {{ $isCicilan ? 'Menunggu DP' : 'Menunggu Pembayaran' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Bank accounts --}}
        @if (count($bankAccounts) > 0)
            <div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm mb-6">
                <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i data-lucide="landmark" class="w-5 h-5 text-primary-500"></i>
                    Transfer ke Rekening
                </h3>
                <div class="space-y-3">
                    @foreach ($bankAccounts as $acc)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-10 h-10 rounded-lg bg-{{ $acc['logo_color'] ?? 'slate' }}-100 flex items-center justify-center shrink-0">
                                <i data-lucide="building-2" class="w-5 h-5 text-{{ $acc['logo_color'] ?? 'slate' }}-600"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-sm text-slate-900">{{ $acc['bank'] }}</p>
                                <p class="text-xs text-slate-500">{{ $acc['number'] }} — a.n {{ $acc['holder'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Info box --}}
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-800 mb-6">
            <p class="font-semibold mb-2">📌 Langkah selanjutnya:</p>
            <ol class="list-decimal list-inside space-y-1 text-blue-700">
                <li>Transfer sesuai total pembayaran ke salah satu rekening di atas.</li>
                <li>Simpan bukti transfer (screenshot/foto).</li>
                <li>Klik tombol "Upload Bukti Bayar" di bawah ini.</li>
                <li>Konfirmasi akan dikirim ke WhatsApp setelah diverifikasi.</li>
            </ol>
        </div>

        {{-- CTA Upload --}}
        <div class="text-center space-y-3">
            <a href="{{ $uploadUrl }}"
               class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Upload Bukti Bayar
            </a>
            <br>
            <a href="{{ route('courses.show', $course->slug) }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke halaman kelas
            </a>
        </div>
    </div>
</div>
</x-layouts.store>
