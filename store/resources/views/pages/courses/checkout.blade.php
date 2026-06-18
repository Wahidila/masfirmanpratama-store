<x-layouts.store
    title="Daftar Kelas — {{ $course->title }}"
    description="Formulir pendaftaran kelas {{ $course->title }}."
>
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white py-8 sm:py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-primary-600 transition mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke detail kelas
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Formulir Pendaftaran</h1>
            <p class="mt-2 text-slate-500 text-sm">Isi data di bawah untuk mendaftar kelas</p>
        </div>

        {{-- Course summary card --}}
        <div class="mb-8 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
            @if ($course->image_path)
                <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}"
                     class="w-16 h-16 rounded-xl object-cover shrink-0">
            @endif
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-slate-900 text-sm truncate">{{ $course->title }}</h2>
                @if ($course->subtitle)
                    <p class="text-xs text-slate-500 truncate">{{ $course->subtitle }}</p>
                @endif
            </div>
            <div class="text-right shrink-0">
                <p class="text-lg font-extrabold text-slate-900">Rp {{ number_format((int) $course->price, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Form pendaftaran --}}
        <form method="POST" action="{{ route('courses.checkout.store', $course->slug) }}" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm space-y-5">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-primary-500"></i>
                    Data Pendaftar
                </h3>

                {{-- Nama Lengkap --}}
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        value="{{ old('customer_name') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition"
                        placeholder="Masukkan nama lengkap"
                    >
                    @error('customer_name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="customer_email"
                        name="customer_email"
                        value="{{ old('customer_email') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition"
                        placeholder="email@contoh.com"
                    >
                    @error('customer_email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor WhatsApp --}}
                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1">
                        Nomor WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="tel"
                        id="customer_phone"
                        name="customer_phone"
                        value="{{ old('customer_phone') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition"
                        placeholder="08xxxxxxxxxx"
                        inputmode="numeric"
                    >
                    @error('customer_phone')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pekerjaan --}}
                <div>
                    <label for="occupation" class="block text-sm font-medium text-slate-700 mb-1">
                        Pekerjaan / Profesi
                    </label>
                    <input
                        type="text"
                        id="occupation"
                        name="occupation"
                        value="{{ old('occupation') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition"
                        placeholder="Mahasiswa, Karyawan, Wirausaha, dll"
                    >
                </div>

                {{-- Motivasi --}}
                <div>
                    <label for="motivation" class="block text-sm font-medium text-slate-700 mb-1">
                        Motivasi Mengikuti Kelas
                    </label>
                    <textarea
                        id="motivation"
                        name="motivation"
                        rows="3"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition resize-none"
                        placeholder="Ceritakan singkat alasan kamu ingin ikut kelas ini..."
                    >{{ old('motivation') }}</textarea>
                </div>
            </div>

            {{-- Summary + Submit --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-sm" x-data="courseCheckout({{ json_encode($schemes) }}, {{ (int) $course->price }})">

                {{-- Metode Pembayaran --}}
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2 mb-4">
                    <i data-lucide="wallet" class="w-5 h-5 text-primary-500"></i>
                    Metode Pembayaran
                </h3>

                <div class="space-y-3 mb-6">
                    {{-- Lunas --}}
                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition"
                           :class="paymentType === 'lunas' ? 'border-primary-500 bg-primary-50' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="payment_type" value="lunas"
                               x-model="paymentType"
                               class="mt-0.5 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="font-semibold text-sm text-slate-900">Bayar Lunas</span>
                            <p class="text-xs text-slate-500 mt-0.5">Bayar langsung full amount via transfer bank.</p>
                            <p class="text-sm font-bold text-slate-900 mt-1">Rp {{ number_format((int) $course->price, 0, ',', '.') }}</p>
                        </div>
                    </label>

                    {{-- Cicilan (hanya tampil kalau ada skema) --}}
                    @if ($schemes->count() > 0)
                        <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition"
                               :class="paymentType === 'cicilan' ? 'border-primary-500 bg-primary-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_type" value="cicilan"
                                   x-model="paymentType"
                                   class="mt-0.5 text-primary-600 focus:ring-primary-500">
                            <div class="flex-1">
                                <span class="font-semibold text-sm text-slate-900">Bayar Cicilan</span>
                                <p class="text-xs text-slate-500 mt-0.5">DP di awal + cicilan bulanan.</p>
                            </div>
                        </label>

                        {{-- Pilihan skema cicilan --}}
                        <div x-show="paymentType === 'cicilan'" x-collapse class="space-y-3 pl-2">
                            @foreach ($schemes as $scheme)
                                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition"
                                       :class="selectedScheme == {{ $scheme->id }} ? 'border-primary-400 bg-primary-25' : 'border-slate-100 hover:border-slate-200'">
                                    <input type="radio" name="installment_scheme_id" value="{{ $scheme->id }}"
                                           x-model="selectedScheme"
                                           class="mt-0.5 text-primary-600 focus:ring-primary-500">
                                    <div>
                                        <span class="font-medium text-sm text-slate-900">{{ $scheme->name }}</span>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            DP {{ number_format((float) $scheme->dp_pct, 0) }}%
                                            (Rp {{ number_format((int) ceil((int) $course->price * (float) $scheme->dp_pct / 100), 0, ',', '.') }})
                                            + {{ $scheme->n_installments }}x cicilan
                                        </p>
                                    </div>
                                </label>
                            @endforeach

                            {{-- Simulasi jadwal --}}
                            <div x-show="selectedScheme" class="rounded-xl bg-slate-50 border border-slate-100 p-4 mt-2">
                                <p class="text-xs font-semibold text-slate-700 mb-2">📅 Simulasi Jadwal Pembayaran:</p>
                                <template x-for="(item, idx) in schedule" :key="idx">
                                    <div class="flex justify-between text-xs py-1 border-b border-slate-100 last:border-0">
                                        <span class="text-slate-600" x-text="item.label"></span>
                                        <span class="font-semibold text-slate-900" x-text="'Rp ' + item.amount.toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    @endif
                </div>

                <hr class="border-slate-100 mb-4">

                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-slate-500">Total Pembayaran</span>
                    <span class="text-xl font-extrabold text-slate-900">Rp {{ number_format((int) $course->price, 0, ',', '.') }}</span>
                </div>
                <div x-show="paymentType === 'cicilan' && selectedScheme" class="flex justify-between items-center mb-4">
                    <span class="text-sm text-slate-500">Bayar Sekarang (DP)</span>
                    <span class="text-lg font-bold text-primary-600" x-text="'Rp ' + dpAmount.toLocaleString('id-ID')"></span>
                </div>
                <p class="text-xs text-slate-400 mb-5">
                    Detail pembayaran dan nomor rekening akan dikirim ke WhatsApp kamu setelah pendaftaran berhasil.
                </p>
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:bg-primary-700 transition"
                >
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Daftar & Bayar Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.store>

<script>
function courseCheckout(schemes, totalPrice) {
    return {
        paymentType: 'lunas',
        selectedScheme: null,
        schemes: schemes,
        totalPrice: totalPrice,

        get dpAmount() {
            const scheme = this.schemes.find(s => s.id == this.selectedScheme);
            if (!scheme) return 0;
            return Math.ceil(this.totalPrice * (parseFloat(scheme.dp_pct) / 100));
        },

        get schedule() {
            const scheme = this.schemes.find(s => s.id == this.selectedScheme);
            if (!scheme) return [];

            const dp = Math.ceil(this.totalPrice * (parseFloat(scheme.dp_pct) / 100));
            const remaining = this.totalPrice - dp;
            const perInstallment = Math.ceil(remaining / scheme.n_installments);

            const items = [{ label: 'DP (Bayar Sekarang)', amount: dp }];

            for (let i = 1; i <= scheme.n_installments; i++) {
                const amount = (i === scheme.n_installments)
                    ? remaining - (perInstallment * (scheme.n_installments - 1))
                    : perInstallment;
                const days = scheme.interval_days * i;
                items.push({
                    label: `Cicilan ke-${i} (H+${days})`,
                    amount: Math.max(0, amount)
                });
            }

            return items;
        }
    };
}
</script>
