@extends('layouts.affiliate')

@section('title', 'Program Affiliate MasFirmanPratama')
@section('description', 'Gabung program affiliate MasFirmanPratama — promosikan kelas & buku Mind Power, dapatkan komisi 10% setiap penjualan berhasil.')

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-slate-900 text-white">
    {{-- Dekorasi latar --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-secondary-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-accent-500/15 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-32">
        <div class="max-w-3xl mx-auto text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-sm font-medium text-white/90 mb-6">
                <i data-lucide="trending-up" class="w-4 h-4"></i>
                Komisi hingga 10% per penjualan
            </span>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight mb-6">
                Hasilkan Uang dengan Mempromosikan
                <span class="text-gradient bg-gradient-to-r from-secondary-400 to-accent-400 bg-clip-text text-transparent">Kelas & Buku Mind Power</span>
            </h1>

            <p class="text-lg sm:text-xl text-white/80 mb-10 max-w-2xl mx-auto leading-relaxed">
                Bergabung sebagai affiliator MasFirmanPratama. Dapatkan link referral unik, bagikan ke jaringan Anda, dan terima komisi setiap kali ada pembelian melalui link Anda.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('affiliate.register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-primary-700 font-bold rounded-full shadow-xl hover:shadow-2xl hover:bg-slate-50 transition-all text-base">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    Gabung Sekarang
                </a>
                <a href="#cara-kerja" class="inline-flex items-center gap-2 px-6 py-3 text-white/90 font-medium hover:text-white transition-colors text-base">
                    Pelajari Cara Kerja
                    <i data-lucide="arrow-down" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Benefit --}}
<section id="benefit" class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Keuntungan Menjadi Affiliator</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">Nikmati berbagai benefit menarik saat bergabung sebagai mitra affiliate kami.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Kartu 1 --}}
            <div class="group relative bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:border-primary-200 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                    <i data-lucide="percent" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Komisi 10%</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Dapatkan komisi 10% dari setiap penjualan yang berhasil melalui link referral Anda.</p>
            </div>

            {{-- Kartu 2 --}}
            <div class="group relative bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:border-secondary-200 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-secondary-100 text-secondary-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-secondary-600 group-hover:text-white transition-colors">
                    <i data-lucide="link" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Tracking Otomatis</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Sistem mencatat setiap klik dan pembelian secara otomatis. Cookie berlaku 30 hari.</p>
            </div>

            {{-- Kartu 3 --}}
            <div class="group relative bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:border-accent-200 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-accent-100 text-accent-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent-600 group-hover:text-white transition-colors">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Pencairan Mudah</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Withdraw komisi langsung ke rekening bank Anda setelah masa cooling 7 hari.</p>
            </div>

            {{-- Kartu 4 --}}
            <div class="group relative bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:border-rose-200 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                    <i data-lucide="image" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Materi Marketing</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Akses banner, copywriting, dan konten promosi siap pakai untuk mempermudah penjualan.</p>
            </div>
        </div>
    </div>
</section>

{{-- Cara Kerja --}}
<section id="cara-kerja" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Cara Kerja Program Affiliate</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">Hanya 4 langkah mudah untuk mulai menghasilkan komisi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @php
                $steps = [
                    ['icon' => 'user-plus', 'title' => 'Daftar Akun', 'desc' => 'Buat akun affiliator gratis. Pilih tipe sesuai status Anda (Alumni, Non-Alumni, atau Peserta).'],
                    ['icon' => 'link-2', 'title' => 'Dapatkan Link', 'desc' => 'Setelah akun aktif, generate link referral unik dari dashboard Anda.'],
                    ['icon' => 'share-2', 'title' => 'Bagikan & Promosi', 'desc' => 'Bagikan link ke media sosial, grup, atau kontak personal Anda.'],
                    ['icon' => 'banknote', 'title' => 'Terima Komisi', 'desc' => 'Setiap pembelian via link Anda tercatat otomatis. Komisi masuk setelah 7 hari cooling.'],
                ];
            @endphp

            @foreach ($steps as $i => $step)
                <div class="relative text-center">
                    {{-- Nomor step --}}
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-primary-600 text-white rounded-2xl shadow-lg shadow-primary-500/30 mb-4">
                        <i data-lucide="{{ $step['icon'] }}" class="w-6 h-6"></i>
                    </div>
                    <span class="absolute top-0 right-1/2 translate-x-1/2 -translate-y-2 bg-accent-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">{{ $i + 1 }}</span>
                    <h3 class="font-bold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Tipe Affiliator --}}
<section id="tipe-affiliator" class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Pilih Tipe Affiliator Anda</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">Tiga tipe keanggotaan dengan benefit berbeda sesuai hubungan Anda dengan MasFirmanPratama.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Alumni --}}
            <div class="relative bg-gradient-to-b from-primary-50 to-white rounded-2xl border border-primary-200 p-8 shadow-sm">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-bold mb-4">Alumni AMC</span>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Alumni</h3>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">Sudah pernah mengikuti kelas AMC (Reguler/Privat/Platinum). Dapatkan akses penuh ke semua fitur affiliate.</p>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0"></i> Komisi 10% per penjualan</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0"></i> Dashboard & link referral</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0"></i> Materi marketing eksklusif</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0"></i> Leaderboard & event gamifikasi</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0"></i> Withdraw ke rekening bank</li>
                </ul>
            </div>

            {{-- Non-Alumni --}}
            <div class="relative bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold mb-4">Non-Alumni</span>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Non-Alumni</h3>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">Belum pernah ikut kelas AMC, tapi ingin mempromosikan produk MasFirmanPratama.</p>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Komisi 10% per penjualan</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Dashboard & link referral</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Materi marketing standar</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Withdraw ke rekening bank</li>
                    <li class="flex items-start gap-2 text-slate-400"><i data-lucide="x" class="w-4 h-4 mt-0.5 flex-shrink-0"></i> Leaderboard & event (khusus alumni/peserta)</li>
                </ul>
            </div>

            {{-- Peserta --}}
            <div class="relative bg-gradient-to-b from-secondary-50 to-white rounded-2xl border border-secondary-200 p-8 shadow-sm">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-secondary-100 text-secondary-700 text-xs font-bold mb-4">Peserta Aktif</span>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Peserta</h3>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">Sedang mengikuti kelas AMC saat ini. Promosikan sambil belajar, dapatkan benefit tambahan.</p>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Komisi 10% per penjualan</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Dashboard & link referral</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Materi marketing eksklusif</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Leaderboard & event gamifikasi</li>
                    <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-secondary-600 mt-0.5 flex-shrink-0"></i> Withdraw ke rekening bank</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-slate-600">Temukan jawaban seputar program affiliate kami.</p>
        </div>

        <div class="space-y-3" x-data="{ active: null }">
            @php
                $faqs = [
                    ['q' => 'Apakah mendaftar program affiliate gratis?', 'a' => 'Ya, pendaftaran sepenuhnya gratis. Anda hanya perlu membuat akun, memilih tipe affiliator, dan verifikasi email untuk mulai mendapatkan link referral.'],
                    ['q' => 'Berapa besar komisi yang saya dapatkan?', 'a' => 'Saat ini komisi sebesar 10% dari total pembelian yang berhasil melalui link referral Anda. Komisi berlaku untuk semua produk (kelas dan buku).'],
                    ['q' => 'Kapan komisi bisa dicairkan?', 'a' => 'Komisi bisa dicairkan setelah melewati masa cooling 7 hari sejak transaksi terverifikasi. Pencairan langsung ke rekening bank yang Anda daftarkan.'],
                    ['q' => 'Berapa lama cookie referral berlaku?', 'a' => 'Cookie referral berlaku selama 30 hari. Artinya, jika seseorang mengklik link Anda dan melakukan pembelian dalam 30 hari, Anda tetap mendapatkan komisi.'],
                    ['q' => 'Apa bedanya tipe Alumni, Non-Alumni, dan Peserta?', 'a' => 'Ketiga tipe mendapat komisi yang sama (10%). Perbedaannya: Alumni dan Peserta mendapat akses ke leaderboard, event gamifikasi, dan materi marketing eksklusif. Non-Alumni mendapat materi marketing standar tanpa akses event.'],
                ];
            @endphp

            @foreach ($faqs as $i => $faq)
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <button
                        type="button"
                        @click="active = active === {{ $i }} ? null : {{ $i }}"
                        :aria-expanded="active === {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-inset"
                    >
                        <span class="font-semibold text-slate-900 text-sm sm:text-base pr-4">{{ $faq['q'] }}</span>
                        <i
                            data-lucide="chevron-down"
                            class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200"
                            :class="active === {{ $i }} ? 'rotate-180' : ''"
                        ></i>
                    </button>
                    <div
                        x-show="active === {{ $i }}"
                        x-collapse
                        x-cloak
                    >
                        <div class="px-6 pb-4 text-sm text-slate-600 leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Penutup --}}
<section class="py-20 sm:py-24 bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold mb-4">Siap Mulai Menghasilkan?</h2>
        <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">Daftar sekarang dan mulai promosikan produk Mind Power. Tanpa modal, tanpa risiko — hanya komisi menarik menanti Anda.</p>
        <a href="{{ route('affiliate.register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-primary-700 font-bold rounded-full shadow-xl hover:shadow-2xl hover:bg-slate-50 transition-all text-base">
            <i data-lucide="rocket" class="w-5 h-5"></i>
            Daftar Sebagai Affiliator
        </a>
    </div>
</section>
@endsection
