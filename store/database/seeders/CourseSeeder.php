<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the AMC Reguler course.
     *
     * Since B4 the kelas data is hardcoded here (previously read from
     * config/products.php which no longer carries kelas entries).
     */
    public function run(): void
    {
        Course::updateOrCreate(
            ['slug' => 'kelas-amc-reguler'],
            [
                'title' => 'Kelas Reguler Alpha Mind Control',
                'subtitle' => 'Kelas Reguler Banyak Orang sesuai jadwal yang diatur oleh admin. Tersedia format online via Zoom eksklusif satu persatu (berdasarkan antrian) atau kelas terpusat di berbagai wilayah (offline).',
                'price' => 4500000,
                'original_price' => null,
                'status' => 'active',
                'image_path' => 'images/firman-foto.webp',
                'badge' => 'Paling Diminati',
                'badge_icon' => 'award',
                'category_label' => 'Kelas Reguler',
                'rating' => '4.9/5',
                'student_count' => '1000+',
                'tagline' => 'Mengubah hidup berawal dari mengubah pikiran bawah sadar Anda dengan cara yang tepat dan logis.',
                'installment_available' => true,
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
                'meta_seo' => [
                    'subtitle' => 'Kelas Reguler Banyak Orang sesuai jadwal yang diatur oleh admin. Tersedia format online via Zoom eksklusif satu persatu (berdasarkan antrian) atau kelas terpusat di berbagai wilayah (offline).',
                    'tagline' => 'Mengubah hidup berawal dari mengubah pikiran bawah sadar Anda dengan cara yang tepat dan logis.',
                    'badge' => 'Paling Diminati',
                    'category_label' => 'Kelas Reguler',
                    'image_alt' => 'Kelas Reguler Alpha Mind Control bersama Firman Pratama',
                    'rating' => '4.9/5',
                    'student_count' => '1000+',
                ],
                // Card fields (sync-c1)
                'sort_order' => 1,
                'show_on_homepage' => true,
                'card_style' => 'default',
                'card_icon' => 'video',
                'card_icon_color' => 'text-blue-600',
                'cta_label' => 'Daftar Reguler',
                'card_features' => [
                    '20 Materi Alpha Mind Control',
                    'Modul materi AMC',
                    'Buku Alpha Telepati (luring) / ebook (daring)',
                    'Sertifikat & alat tulis (offline)',
                    'Grup Telegram alumni + pertemuan bulanan',
                    'Jadwal online sesuai antrian',
                ],
            ],
        );

        // ── Kelas Privat ──────────────────────────────────────────────────
        Course::updateOrCreate(
            ['slug' => 'kelas-amc-privat'],
            [
                'title' => 'Kelas Privat Alpha Mind Control',
                'subtitle' => 'Sesi 1-on-1 eksklusif bersama Firman Pratama. Materi disesuaikan dengan masalah dan tujuan pribadi Anda, jadwal lebih fleksibel, pendampingan lebih mendalam.',
                'price' => 7500000,
                'original_price' => null,
                'status' => 'active',
                'image_path' => 'images/firman-foto.webp',
                'badge' => 'Terlaris',
                'badge_icon' => 'star',
                'category_label' => 'Kelas Privat',
                'rating' => '5/5',
                'student_count' => '500+',
                'tagline' => 'Transformasi personal 1-on-1 — masalah Anda unik, solusinya harus personal.',
                'installment_available' => true,
                'description' => [
                    'Kelas Privat AMC memberikan pengalaman transformasi yang sepenuhnya personal. Anda berhadapan langsung satu-satu dengan Firman Pratama — tidak ada peserta lain, tidak ada distraksi.',
                    'Materi disesuaikan dengan masalah spesifik yang sedang Anda hadapi: apakah itu kebuntuan karier, konflik rumah tangga, trauma masa lalu, atau hambatan finansial. Firman akan merancang pendekatan yang tepat untuk kondisi Anda.',
                    'Jadwal jauh lebih fleksibel dan durasi lebih pendek karena fokus hanya pada Anda. Cocok untuk eksekutif, pengusaha, atau siapa saja yang menginginkan privasi dan kecepatan hasil.',
                ],
                'syllabus' => [
                    'Asesmen mendalam pola pikir pribadi',
                    'Identifikasi akar masalah spesifik peserta',
                    'Reset data negatif di pikiran bawah sadar',
                    'Teknik Alpha Telepati untuk pengaruh personal',
                    'Reprogramming tujuan hidup yang terukur',
                    'Visualisasi personal sesuai konteks peserta',
                    'Self-talk konstruktif untuk masalah individu',
                    'Penonaktifan mental block spesifik',
                    'Latihan pernapasan & gelombang Alpha privat',
                    'Komunikasi intensif dengan diri sendiri',
                    'Afirmasi sugestif yang dipersonalisasi',
                    'Membangun mental kelimpahan dari nol',
                    'Strategi jangka panjang pasca sesi',
                    'Mentoring privat follow-up',
                    'Praktik integrasi 30 hari terbimbing',
                ],
                'schedule' => [
                    ['title' => 'Sesi Privat 1-on-1', 'detail' => 'Jadwal fleksibel, disepakati langsung dengan Mas Firman.'],
                    ['title' => 'Lokasi', 'detail' => 'Offline di kota terpilih atau sesuai kesepakatan.'],
                ],
                'benefits' => [
                    ['icon' => 'user-check', 'title' => 'Sesi 1-on-1 Eksklusif', 'desc' => 'Full attention dari Firman Pratama, tanpa peserta lain.'],
                    ['icon' => 'target', 'title' => 'Materi Custom Sesuai Masalah', 'desc' => 'Kurikulum dirancang khusus untuk kondisi dan tujuan Anda.'],
                    ['icon' => 'clock', 'title' => 'Jadwal Fleksibel & Cepat', 'desc' => 'Tidak perlu menunggu batch — langsung mulai kapan saja.'],
                    ['icon' => 'book', 'title' => 'Buku Alpha Telepati + Modul', 'desc' => 'Buku fisik (luring) atau ebook (daring) plus modul privat.'],
                    ['icon' => 'users', 'title' => 'Grup VIP Telegram Alumni', 'desc' => 'Akses komunitas alumni seumur hidup untuk diskusi & support.'],
                    ['icon' => 'shield-check', 'title' => 'Garansi 100% Halal & Logis', 'desc' => 'Pendekatan ilmiah, tanpa hal gaib/mistis.'],
                ],
                'testimonials' => [
                    ['name' => 'Budi H.', 'role' => 'CEO Startup Jakarta', 'quote' => 'Saya skeptis awalnya, tapi sesi privat dengan Mas Firman benar-benar mengubah cara saya mengambil keputusan bisnis. Revenue naik 3x dalam 6 bulan.'],
                    ['name' => 'Sari M.', 'role' => 'Dokter Spesialis', 'quote' => 'Masalah saya spesifik — burnout dan konflik keluarga. Materi yang diberikan sangat personal dan langsung relevan. Luar biasa.'],
                    ['name' => 'Agus P.', 'role' => 'Investor Properti', 'quote' => 'Privat class ini investasi terbaik tahun ini. Mindset saya soal uang dan risiko berubah total. Sekarang lebih tenang dan terarah.'],
                ],
                'related' => ['10-keajaiban-pikiran', 'alpha-telepathy', 'kitab-101-kalimat-sugesti-ajaib'],
                'meta_seo' => [
                    'subtitle' => 'Sesi 1-on-1 eksklusif bersama Firman Pratama. Materi disesuaikan dengan masalah dan tujuan pribadi Anda.',
                    'tagline' => 'Transformasi personal 1-on-1 — masalah Anda unik, solusinya harus personal.',
                    'badge' => 'Terlaris',
                    'category_label' => 'Kelas Privat',
                    'image_alt' => 'Kelas Privat Alpha Mind Control 1-on-1 bersama Firman Pratama',
                    'rating' => '5/5',
                    'student_count' => '500+',
                ],
                // Card fields (sync-c1)
                'sort_order' => 2,
                'show_on_homepage' => true,
                'card_style' => 'highlight',
                'card_icon' => 'mic',
                'card_icon_color' => 'text-accent-600',
                'cta_label' => 'Daftar Privat',
                'card_features' => [
                    '20 Materi AMC (sama dengan reguler)',
                    'Sesi 1-on-1 offline eksklusif',
                    'Materi spesifik sesuai masalah pribadi',
                    'Sertifikat & alat tulis',
                    'Grup Telegram alumni + pertemuan bulanan',
                    'Jadwal lebih fleksibel & lebih cepat',
                ],
            ],
        );

        // ── Kelas Platinum ─────────────────────────────────────────────────
        Course::updateOrCreate(
            ['slug' => 'kelas-amc-platinum'],
            [
                'title' => 'Kelas Platinum Alpha Mind Control',
                'subtitle' => 'Program residensial intensif 3 hari 2 malam. Bongkar semua penghambat diri, percepat transformasi total, pulang sebagai versi terbaik Anda.',
                'price' => 22500000,
                'original_price' => null,
                'status' => 'active',
                'image_path' => 'images/firman-foto.webp',
                'badge' => 'Eksklusif',
                'badge_icon' => 'gem',
                'category_label' => 'Kelas Platinum',
                'rating' => '5/5',
                'student_count' => '100+',
                'tagline' => '3 hari 2 malam yang mengubah sisa hidup Anda — transformasi total tanpa kompromi.',
                'installment_available' => true,
                'description' => [
                    'Kelas Platinum AMC adalah program transformasi paling intensif yang ditawarkan Firman Pratama. Selama 3 hari 2 malam, Anda tinggal di hotel bersama Mas Firman dan sepenuhnya fokus pada pembenahan pikiran bawah sadar.',
                    'Program ini dirancang untuk membongkar penghambat diri secara menyeluruh — trauma lama, limiting beliefs, pola sabotase diri — dan menggantinya dengan fondasi mental yang kokoh. Durasi panjang memungkinkan konsultasi privat mendalam yang tidak bisa didapat di kelas reguler maupun privat singkat.',
                    'Kuota sangat terbatas untuk menjaga kualitas interaksi. Semua kebutuhan (hotel, makan 3x sehari, modul platinum) sudah termasuk. Anda hanya perlu datang dengan komitmen untuk berubah.',
                ],
                'syllabus' => [
                    'Deep assessment & mapping pikiran bawah sadar',
                    'Identifikasi & eliminasi root cause mental block',
                    'Reset total data negatif yang mengakar',
                    'Advanced Alpha Telepati — teknik pengaruh tingkat lanjut',
                    'Reprogramming identitas diri baru',
                    'Visualisasi tingkat lanjut dengan anchor emosional',
                    'Advanced self-talk & inner dialogue mastery',
                    'Penonaktifan permanen mental block kompleks',
                    'Mastery gelombang Alpha & Theta',
                    'Komunikasi bawah sadar tingkat lanjut',
                    'Afirmasi sugestif advanced multi-layer',
                    'Abundance mindset — dari konsep ke realitas',
                    'Blueprint kehidupan baru pasca program',
                    'Konsultasi privat mendalam selama residensial',
                    'Strategi implementasi 90 hari terbimbing',
                ],
                'schedule' => [
                    ['title' => 'Program Residensial', 'detail' => '3 hari 2 malam di hotel. Tanggal periodik, kuota terbatas.'],
                    ['title' => 'Konsultasi Privat', 'detail' => 'Sesi privat mendalam selama program berlangsung.'],
                ],
                'benefits' => [
                    ['icon' => 'hotel', 'title' => 'Hotel 3 Hari 2 Malam', 'desc' => 'Akomodasi nyaman, semua kebutuhan selama program ditanggung.'],
                    ['icon' => 'utensils', 'title' => 'Makan 3x Sehari', 'desc' => 'Konsumsi terjamin agar Anda fokus sepenuhnya pada materi.'],
                    ['icon' => 'book-open', 'title' => 'Modul Platinum Eksklusif', 'desc' => 'Materi advanced yang tidak tersedia di kelas reguler/privat.'],
                    ['icon' => 'user-check', 'title' => 'Konsultasi Privat Mendalam', 'desc' => 'Sesi personal dengan Firman Pratama selama program.'],
                    ['icon' => 'users', 'title' => 'Komunitas Platinum Seumur Hidup', 'desc' => 'Jaringan eksklusif alumni platinum untuk kolaborasi & support.'],
                    ['icon' => 'shield-check', 'title' => 'Garansi Transformasi', 'desc' => 'Follow-up 90 hari pasca program untuk memastikan hasil bertahan.'],
                ],
                'testimonials' => [
                    ['name' => 'Hendra W.', 'role' => 'Pengusaha Properti Surabaya', 'quote' => '3 hari platinum setara 3 bulan coaching reguler. Masalah yang saya anggap permanent selesai dalam satu sesi malam. Life changing.'],
                    ['name' => 'Lina K.', 'role' => 'Motivator & Trainer', 'quote' => 'Saya sudah ikut banyak seminar, tapi kedalaman materi Platinum AMC ini berbeda level. Pulang dari sini saya langsung bisa upgrade bisnis dan hidup saya.'],
                    ['name' => 'Rudi T.', 'role' => 'Direktur Perusahaan Manufaktur', 'quote' => 'Investasi 22.5jt terasa kecil dibanding hasilnya. Dalam 3 hari saya menemukan akar masalah yang menghambat saya 15 tahun. Sekarang revenue perusahaan naik 40%.'],
                ],
                'related' => ['10-keajaiban-pikiran', 'alpha-telepathy', 'kitab-101-kalimat-sugesti-ajaib'],
                'meta_seo' => [
                    'subtitle' => 'Program residensial intensif 3 hari 2 malam. Bongkar semua penghambat diri, percepat transformasi total.',
                    'tagline' => '3 hari 2 malam yang mengubah sisa hidup Anda — transformasi total tanpa kompromi.',
                    'badge' => 'Eksklusif',
                    'category_label' => 'Kelas Platinum',
                    'image_alt' => 'Kelas Platinum Alpha Mind Control program residensial 3 hari 2 malam',
                    'rating' => '5/5',
                    'student_count' => '100+',
                ],
                // Card fields (sync-c1)
                'sort_order' => 3,
                'show_on_homepage' => true,
                'card_style' => 'dark',
                'card_icon' => 'gem',
                'card_icon_color' => 'text-secondary-400',
                'cta_label' => 'Pilih Platinum',
                'card_features' => [
                    'Materi advanced',
                    'Hotel 3 hari 2 malam',
                    'Makan 3x sehari',
                    'Tugas terstruktur selama pelatihan',
                    'Modul Platinum + alat tulis',
                    'Durasi panjang untuk konsultasi privat',
                ],
            ],
        );
    }
}
