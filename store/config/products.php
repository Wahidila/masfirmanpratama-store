<?php

/*
|--------------------------------------------------------------------------
| Product registry (M1 placeholder data)
|--------------------------------------------------------------------------
|
| Data produk untuk halaman katalog dan detail (M1 — sebelum Eloquent).
| Akan di-deprecate di M2 saat ada model `Product` + tabel DB; bentuk data
| sengaja mirror calon kolom DB (slug, type, price, original_price, dst.).
|
| Skema dipakai oleh:
|   - resources/views/pages/products/show.blade.php (dispatcher)
|   - resources/views/pages/products/course.blade.php (template kelas)
|   - resources/views/pages/products/book.blade.php   (template buku — task #8)
|
| Pakai helper:
|   $product = config('products.items.' . $slug);
|   $items   = config('products.items');
|
| Catatan koordinasi paralel:
|   Sibling task t_c139b693 (book detail) memakai schema sendiri di
|   config/catalog.php sambil bekerja paralel. Dispatcher di show.blade.php
|   sengaja menerima `$slug` dan resolve dari `config('products.items')`,
|   sedangkan kalau template per-type belum ada `view()->exists()` jatuhkan
|   ke placeholder. Saat task buku selesai, harmonisasi schema bisa
|   dilakukan di review handoff.
|
*/

return [

    'items' => [

        // ─── KELAS ──────────────────────────────────────────────────────────

        'kelas-amc-reguler' => [
            'slug' => 'kelas-amc-reguler',
            'type' => 'kelas',
            'title' => 'Kelas Reguler Alpha Mind Control',
            'subtitle' => 'Kelas Reguler Banyak Orang sesuai jadwal yang diatur oleh admin. Tersedia format online via Zoom eksklusif satu persatu (berdasarkan antrian) atau kelas terpusat di berbagai wilayah (offline).',
            'badge' => 'Paling Diminati',
            'badge_icon' => 'award',
            'category_label' => 'Kelas Reguler',
            'price' => 4500000,
            'original_price' => null,
            'image' => 'images/firman-foto.webp',
            'image_alt' => 'Kelas Reguler Alpha Mind Control bersama Firman Pratama',
            'cta_label' => 'Daftar Sekarang',
            'cta_href' => null,                 // null → route('checkout.index')
            'installment_available' => true,
            'rating' => '4.9/5',
            'student_count' => '1000+',
            'tagline' => 'Mengubah hidup berawal dari mengubah pikiran bawah sadar Anda dengan cara yang tepat dan logis.',
            'description' => [
                'Kelas Reguler AMC adalah jalur paling banyak diminati untuk masuk ke metode Alpha Mind Control — sebuah pendekatan logis dan ilmiah untuk memodifikasi pola pikir bawah sadar.',
                'Anda akan dibimbing langsung oleh Firman Pratama untuk memahami struktur pikiran, teknik komunikasi dengan bawah sadar, dan latihan-latihan praktis yang langsung bisa diterapkan ke karier, bisnis, dan kehidupan personal.',
                'Format fleksibel: pilih online via Zoom (eksklusif, antrian) atau offline di kota-kota terpilih. Materi tetap sama, akses komunitas alumni seumur hidup.',
            ],
            'syllabus' => [
                'Memahami benar apa itu pikiran bawah sadar',
                'Memahami cara memodifikasi data dalam pikiran',
                'Mengetahui letak "HATI" sebenarnya',
                'Teknik Alpha Telepati (mempengaruhi orang lain)',
                'Teknik "tarik uang gaib" yang Halal',
                'Cara membuat kalimat doa yang 99% terkabul',
                'Membersihkan emosi negatif yang menghambat',
                'Memprogram tujuan ke pikiran bawah sadar',
                'Teknik visualisasi efektif untuk hasil nyata',
                'Mengaktifkan intuisi dan firasat',
                'Self-talk konstruktif setiap hari',
                'Menonaktifkan "saklar" rasa malas',
                'Latihan pernapasan untuk masuk ke gelombang Alpha',
                'Kunci komunikasi dengan diri sendiri',
                'Metode "afirmasi sugestif" Firman Pratama',
                'Membangun mental kelimpahan (abundance mindset)',
                'Strategi mempertahankan hasil jangka panjang',
                'Mentoring grup eksklusif setelah kelas',
                'Akses materi update seumur hidup',
                'Praktik integrasi 30 hari pasca kelas',
            ],
            'schedule' => [
                ['title' => 'Batch Online (Zoom)', 'detail' => 'Setiap Sabtu–Minggu, 09.00–16.00 WIB. 4 sesi.'],
                ['title' => 'Offline Surabaya', 'detail' => 'Tanggal menyesuaikan kuota minimum 8 peserta.'],
                ['title' => 'Offline Jakarta', 'detail' => 'Jadwal periodik — konfirmasi ke admin via WhatsApp.'],
                ['title' => 'Offline Malang', 'detail' => 'Berkala — alumni AMC dapat refresh gratis 1x.'],
            ],
            'benefits' => [
                ['icon' => 'book-open', 'title' => 'Akses 20 Modul Materi AMC', 'desc' => 'Materi lengkap dari dasar hingga advance, langsung praktik.'],
                ['icon' => 'book', 'title' => 'Buku Eksklusif Alpha Telepati', 'desc' => 'Dikirim untuk peserta luring; ebook untuk peserta daring.'],
                ['icon' => 'award', 'title' => 'Sertifikat & Alat Tulis', 'desc' => 'Hanya bagi peserta luring, lengkap dengan kit edukasi.'],
                ['icon' => 'users', 'title' => 'Grup VIP Telegram Alumni', 'desc' => 'Akses komunitas alumni seumur hidup untuk diskusi & support.'],
                ['icon' => 'calendar-check', 'title' => 'Pertemuan Rutin Setiap Bulan', 'desc' => 'Refresh materi + Q&A langsung dengan Firman Pratama.'],
                ['icon' => 'shield-check', 'title' => 'Garansi 100% Halal & Logis', 'desc' => 'Pendekatan ilmiah, tanpa hal gaib/mistis.'],
            ],
            'testimonials' => [
                ['name' => 'Rendy A.', 'role' => 'Owner UMKM Surabaya', 'quote' => 'Setelah ikut kelas AMC, omzet naik 2x lipat dalam 4 bulan. Yang berubah cara saya berpikir tentang uang dan diri sendiri.'],
                ['name' => 'Diah N.', 'role' => 'HRD Manager', 'quote' => 'Materi tarik uang gaib yang halal itu logis banget kalau sudah dipraktikkan. Sekarang saya jauh lebih tenang.'],
                ['name' => 'Yanto S.', 'role' => 'Sales Insurance', 'quote' => 'Teknik Alpha Telepati ngebantu closing saya tembus rekor pribadi. Ini bukan magic — ini disiplin pikiran.'],
            ],
            'related' => ['10-keajaiban-pikiran', 'alpha-telepathy', 'kitab-101-kalimat-sugesti-ajaib'],
        ],

        // ─── BUKU (FE detail port — task t_c139b693) ────────────────────────
        // 6 entries: 5 buku ber-cover + 1 placeholder pre-order. Field tambahan
        // yang dipakai book.blade.php: description, specs, preview_pages, badge,
        // badge_icon, tagline, cta_label. Schema kompatibel dengan course.blade.php
        // (sibling task) sehingga product-card di halaman kelas tetap render OK.

        '10-keajaiban-pikiran' => [
            'slug' => '10-keajaiban-pikiran',
            'type' => 'buku',
            'title' => 'Buku 10 Keajaiban Pikiran',
            'tagline' => 'Best Seller Umum',
            'subtitle' => 'Langkah awal yang esensial membuka pengetahuan absolut tentang keajaiban-keajaiban yang tersembunyi di balik otak dan pikiran bawah sadar.',
            'badge' => 'Best Seller Umum',
            'badge_icon' => 'star',
            'category_label' => 'Buku',
            'price' => 150000,
            'original_price' => 250000,
            'image' => 'images/10-keajaiban-pikiran.webp',
            'image_alt' => 'Cover buku 10 Keajaiban Pikiran',
            'cta_label' => 'Pesan Buku Sekarang',
            'description' => [
                'Buku perdana Firman Pratama yang membuka tabir cara kerja pikiran bawah sadar. Disusun dengan bahasa praktis sehingga pembaca pemula pun langsung bisa mempraktikkan teknik dasar Mind Power di kehidupan sehari-hari.',
                'Cocok untuk Anda yang ingin paham mengapa beberapa orang sukses dengan mudah sementara yang lain stuck — kunci jawabannya ada pada 10 keajaiban pikiran yang dibahas di buku ini.',
            ],
            'preview_pages' => [],
            'specs' => [
                'penulis' => 'Firman Pratama',
                'penerbit' => 'Wahana Sejati',
                'format' => 'Fisik (Buku Cetak)',
                'bahasa' => 'Indonesia',
                'halaman' => '180 halaman',
                'dimensi' => '14 x 21 cm',
                'tahun_terbit' => '2023',
                'isbn' => 'ISBN-placeholder',
            ],
            'related' => ['alpha-telepathy', 'instan-hypnosis', 'kelas-amc-reguler'],
        ],

        'alpha-telepathy' => [
            'slug' => 'alpha-telepathy',
            'type' => 'buku',
            'title' => 'Buku Alpha Telepathy',
            'tagline' => 'Pilihan Editor',
            'subtitle' => 'Teknik komunikasi pikiran bawah sadar untuk mempengaruhi diri sendiri dan orang lain dengan cara halal dan logis.',
            'badge' => 'Pilihan Editor',
            'badge_icon' => 'sparkles',
            'category_label' => 'Buku',
            'price' => 175000,
            'original_price' => null,
            'image' => 'images/alpha-telepathy.webp',
            'image_alt' => 'Cover buku Alpha Telepathy',
            'cta_label' => 'Pesan Buku Sekarang',
            'description' => [
                'Panduan lanjutan metode Alpha Mind Control untuk menguasai komunikasi pikiran-ke-pikiran. Buku ini fokus ke teknik sugesti jarak jauh, telepati, dan visualisasi terarah yang dipakai alumni AMC untuk closing sales, mendamaikan keluarga, hingga negosiasi bisnis.',
                'Setiap teknik disertai studi kasus dari peserta kelas AMC sehingga Anda paham konteks pemakaiannya — bukan sekadar teori abstrak.',
            ],
            'preview_pages' => [],
            'specs' => [
                'penulis' => 'Firman Pratama',
                'penerbit' => 'Wahana Sejati',
                'format' => 'Fisik (Buku Cetak)',
                'bahasa' => 'Indonesia',
                'halaman' => '210 halaman',
                'dimensi' => '14 x 21 cm',
                'tahun_terbit' => '2023',
                'isbn' => 'ISBN-placeholder',
            ],
            'related' => ['10-keajaiban-pikiran', 'instan-hypnosis', 'kelas-amc-reguler'],
        ],

        'instan-hypnosis' => [
            'slug' => 'instan-hypnosis',
            'type' => 'buku',
            'title' => 'Buku Instan Hypnosis',
            'tagline' => 'Best Seller',
            'subtitle' => 'Praktik induksi cepat untuk akses pikiran bawah sadar — diri sendiri maupun orang lain.',
            'badge' => 'Best Seller',
            'badge_icon' => 'flame',
            'category_label' => 'Buku',
            'price' => 165000,
            'original_price' => null,
            'image' => 'images/instan-hypnosis.webp',
            'image_alt' => 'Cover buku Instan Hypnosis',
            'cta_label' => 'Pesan Buku Sekarang',
            'description' => [
                'Teknik hypnosis cepat (rapid induction) yang langsung bisa diterapkan ke interaksi sehari-hari: sales, persuasi, parenting, hingga self-therapy.',
                'Berisi script siap pakai, breakdown step-by-step, dan tip umum menghadapi resistensi subjek — semua dalam framework AMC yang halal dan logis.',
            ],
            'preview_pages' => [],
            'specs' => [
                'penulis' => 'Firman Pratama',
                'penerbit' => 'Wahana Sejati',
                'format' => 'Fisik (Buku Cetak)',
                'bahasa' => 'Indonesia',
                'halaman' => '195 halaman',
                'dimensi' => '14 x 21 cm',
                'tahun_terbit' => '2024',
                'isbn' => 'ISBN-placeholder',
            ],
            'related' => ['10-keajaiban-pikiran', 'alpha-telepathy', 'kelas-amc-reguler'],
        ],

        'kitab-101-kalimat-sugesti-ajaib' => [
            'slug' => 'kitab-101-kalimat-sugesti-ajaib',
            'type' => 'buku',
            'title' => 'Kitab 101 Kalimat Sugesti Ajaib',
            'tagline' => 'Praktis',
            'subtitle' => '101 kalimat sugesti siap pakai untuk memprogram pikiran bawah sadar setiap hari.',
            'badge' => 'Praktis',
            'badge_icon' => 'wand-2',
            'category_label' => 'Buku',
            'price' => 95000,
            'original_price' => 150000,
            'image' => 'images/kitab-101-kalimat-sugesti-ajaib.webp',
            'image_alt' => 'Cover kitab 101 Kalimat Sugesti Ajaib',
            'cta_label' => 'Pesan Buku Sekarang',
            'description' => [
                '101 kalimat sugesti yang sudah teruji efektif dalam sesi mentoring AMC. Setiap kalimat dilengkapi catatan kapan dan bagaimana cara membacanya supaya pesan menembus filter pikiran sadar.',
                'Format buku saku — gampang dibawa, gampang dibaca ulang setiap hari. Cocok untuk dijadikan companion harian setelah Anda selesai baca buku-buku Mind Power lainnya.',
            ],
            'preview_pages' => [],
            'specs' => [
                'penulis' => 'Firman Pratama',
                'penerbit' => 'Wahana Sejati',
                'format' => 'Fisik (Buku Cetak)',
                'bahasa' => 'Indonesia',
                'halaman' => '160 halaman',
                'dimensi' => '13 x 19 cm',
                'tahun_terbit' => '2023',
                'isbn' => 'ISBN-placeholder',
            ],
            'related' => ['kitab-kunci-penarik-rezeki', '10-keajaiban-pikiran', 'kelas-amc-reguler'],
        ],

        'kitab-kunci-penarik-rezeki' => [
            'slug' => 'kitab-kunci-penarik-rezeki',
            'type' => 'buku',
            'title' => 'Kitab Kunci Penarik Rezeki',
            'tagline' => 'Favorit Alumni',
            'subtitle' => 'Doa, afirmasi, dan teknik visualisasi yang dirangkum dalam satu buku saku.',
            'badge' => 'Favorit Alumni',
            'badge_icon' => 'gem',
            'category_label' => 'Buku',
            'price' => 110000,
            'original_price' => null,
            'image' => 'images/kitab-kunci-penarik-rezeki.webp',
            'image_alt' => 'Cover kitab Kunci Penarik Rezeki',
            'cta_label' => 'Pesan Buku Sekarang',
            'description' => [
                'Panduan mengaktifkan kunci-kunci pikiran yang menarik rezeki secara alami: niat, syukur, sedekah, visualisasi finansial, dan teknik AMC khusus untuk pemulihan finansial.',
                'Materi pendamping resmi kelas Alpha Money Magnet — ratusan alumni melaporkan perubahan signifikan dalam kondisi finansial setelah konsisten mempraktikkan isi buku ini selama 30–90 hari.',
            ],
            'preview_pages' => [],
            'specs' => [
                'penulis' => 'Firman Pratama',
                'penerbit' => 'Wahana Sejati',
                'format' => 'Fisik (Buku Cetak)',
                'bahasa' => 'Indonesia',
                'halaman' => '230 halaman',
                'dimensi' => '14 x 21 cm',
                'tahun_terbit' => '2024',
                'isbn' => 'ISBN-placeholder',
            ],
            'related' => ['kitab-101-kalimat-sugesti-ajaib', 'alpha-telepathy', '10-keajaiban-pikiran'],
        ],

        'formula-amc-firman-pratama' => [
            'slug' => 'formula-amc-firman-pratama',
            'type' => 'buku',
            'title' => 'Formula AMC',
            'tagline' => 'Pre-Order',
            'subtitle' => 'Buku resmi dari Firman Pratama — formula lengkap Alpha Mind Control dalam satu buku.',
            'badge' => 'Pre-Order',
            'badge_icon' => 'clock',
            'category_label' => 'Buku',
            'price' => 200000,
            'original_price' => 275000,
            'image' => 'images/formula-amc-firman-pratama.webp',
            'image_alt' => 'Cover buku Formula AMC',
            'cta_label' => 'Pesan Buku Sekarang',
            'description' => [
                'Kompilasi resmi seluruh formula Alpha Mind Control yang sebelumnya hanya diajarkan di kelas reguler. Cocok untuk alumni AMC yang ingin punya referensi lengkap dan untuk pembaca baru yang ingin paham gambaran besar metodenya sebelum ikut kelas.',
                'Edisi pre-order terbatas — pembeli pertama mendapatkan tanda tangan personal Firman Pratama dan akses ke grup Telegram pembaca eksklusif.',
            ],
            'preview_pages' => [],
            'specs' => [
                'penulis' => 'Firman Pratama',
                'penerbit' => 'Wahana Sejati',
                'format' => 'Fisik (Buku Cetak)',
                'bahasa' => 'Indonesia',
                'halaman' => '300+ halaman',
                'dimensi' => '14 x 21 cm',
                'tahun_terbit' => '2026',
                'isbn' => 'TBA',
            ],
            'related' => ['10-keajaiban-pikiran', 'alpha-telepathy', 'kelas-amc-reguler'],
        ],

    ],

];
