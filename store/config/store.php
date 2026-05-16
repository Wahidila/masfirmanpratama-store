<?php

/*
|--------------------------------------------------------------------------
| Store config (M1 placeholder data)
|--------------------------------------------------------------------------
|
| Konfigurasi runtime untuk halaman checkout (M1 — sebelum admin DB-driven).
| Akan di-deprecate / sebagian dipindah ke tabel `settings` + admin form
| di M2 sehingga Klien bebas atur skema cicilan dan ongkir tanpa rilis kode.
|
| Skema dipakai oleh:
|   - resources/views/pages/checkout/index.blade.php
|
| Helper:
|   $schemes = config('store.installment_schemes');
|   $methods = config('store.shipping_methods');
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Skema cicilan
    |--------------------------------------------------------------------------
    |
    | dp_pct  : persentase Down Payment dari total order (0–100)
    | n       : jumlah pembayaran TOTAL (DP + sisa cicilan). Sisa cicilan
    |           setelah DP = n - 1 dan dibagi rata dengan rounding genap.
    |
    | Contoh: total Rp 4.500.000 + 3x Cicilan + DP 30%:
    |   - DP                = Rp 1.350.000 (jatuh tempo: hari ini)
    |   - Cicilan 1 dari 2  = Rp 1.575.000 (jatuh tempo: +1 bulan)
    |   - Cicilan 2 dari 2  = Rp 1.575.000 (jatuh tempo: +2 bulan)
    |
    | KRITIS: skema bebas diatur Klien — frontend HARUS dynamic. Jangan
    | hardcode label / jumlah baris / urutan di view.
    |
    */
    'installment_schemes' => [
        ['name' => '3x Cicilan', 'n' => 3, 'dp_pct' => 30],
        ['name' => '6x Cicilan', 'n' => 6, 'dp_pct' => 20],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metode ongkir (M1 hardcoded, M2 wire ke Agenwebsite.com)
    |--------------------------------------------------------------------------
    |
    | Pricing M1 = nominal flat untuk demo. Kuriri & ETA ditampilkan as-is.
    | Saat M2 wire ke Agenwebsite.com, struktur ini diganti dengan respons
    | API per (kota_asal, kota_tujuan, berat_total).
    |
    */
    'shipping_methods' => [
        ['code' => 'REG', 'label' => 'JNE Reguler — 3 sd 5 hari', 'price' => 25000],
        ['code' => 'YES', 'label' => 'JNE YES — 1 hari', 'price' => 45000],
        ['code' => 'OKE', 'label' => 'JNE OKE — 5 sd 7 hari', 'price' => 18000],
    ],

    /*
    |--------------------------------------------------------------------------
    | Provinsi & kota (M1 static, M2 dari Agenwebsite.com city list)
    |--------------------------------------------------------------------------
    */
    'provinces' => [
        'DKI Jakarta',
        'Jawa Barat',
        'Banten',
        'Jawa Tengah',
        'DI Yogyakarta',
        'Jawa Timur',
        'Bali',
        'Sumatera Utara',
        'Sumatera Barat',
        'Sumatera Selatan',
        'Lampung',
        'Kalimantan Timur',
        'Sulawesi Selatan',
        'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rekening bank (M1 dummy, M2 admin form)
    |--------------------------------------------------------------------------
    |
    | Dipakai oleh halaman checkout success + upload bukti bayar untuk
    | menampilkan instruksi transfer manual. Format ID rekening pakai dash
    | tiap 4 digit supaya gampang dibaca calon transferor.
    |
    | M2 akan di-replace ke tabel `settings` (admin form bisa ubah tanpa
    | rilis kode). Format runtime tetap sama supaya frontend tidak berubah.
    |
    */
    'bank_accounts' => [
        [
            'bank' => 'BCA',
            'number' => '1234-5678-9012',
            'holder' => 'PT. Dummy AMC',
            'logo_color' => 'sky',
        ],
        [
            'bank' => 'Mandiri',
            'number' => '0987-6543-2109',
            'holder' => 'PT. Dummy AMC',
            'logo_color' => 'amber',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp admin (M1 dummy, M2 dari settings)
    |--------------------------------------------------------------------------
    |
    | Customer diarahkan ke WA admin untuk konfirmasi/komplain pasca-transfer.
    | Format E.164 tanpa plus untuk wa.me link.
    |
    */
    'wa_admin' => [
        'number' => '6281234567890',
        'label' => 'Admin Firman Pratama',
    ],

    /*
    |--------------------------------------------------------------------------
    | Provinsi & kota (M1 static, M2 dari Agenwebsite.com city list)
    |--------------------------------------------------------------------------
    | (block dipindah ke bawah supaya bank_accounts dekat config order)
    */
    'cities' => [
        // Jabodetabek
        'Jakarta Pusat',
        'Jakarta Selatan',
        'Jakarta Barat',
        'Jakarta Timur',
        'Jakarta Utara',
        'Bekasi',
        'Bogor',
        'Depok',
        'Tangerang',
        'Tangerang Selatan',
        // Jabar / Banten lain
        'Bandung',
        'Cirebon',
        // Jateng / DIY
        'Semarang',
        'Solo',
        'Yogyakarta',
        // Jatim
        'Surabaya',
        'Malang',
        'Sidoarjo',
        'Kediri',
        // Bali
        'Denpasar',
        // Sumatera
        'Medan',
        'Padang',
        'Palembang',
        'Bandar Lampung',
        // Kalimantan / Sulawesi
        'Balikpapan',
        'Makassar',
        'Lainnya',
    ],

];
