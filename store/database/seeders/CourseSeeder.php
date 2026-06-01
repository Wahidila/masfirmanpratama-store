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
            ],
        );
    }
}
