<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — MasFirmanPratama</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-hidden bg-gray-50 dark:bg-gray-950 font-sans">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-24 top-[-10%] h-96 w-96 rounded-full bg-primary-100 mix-blend-multiply blur-3xl opacity-60 animate-blob"></div>
        <div class="absolute -right-24 top-1/3 h-96 w-96 rounded-full bg-secondary-50 mix-blend-multiply blur-3xl opacity-70 animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-10%] left-1/3 h-80 w-80 rounded-full bg-accent-500/20 mix-blend-multiply blur-3xl opacity-60 animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <main class="flex min-h-screen items-center justify-center px-4">
        <div x-data="{ countdown: 'M2' }" class="glass hover-lift max-w-xl rounded-3xl p-10 text-center shadow-xl animate-fade-in-up">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-primary-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-primary-700">
                <span class="h-2 w-2 rounded-full bg-primary-500 animate-float"></span>
                Coming soon
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-800 dark:text-white/90 sm:text-4xl">
                Admin panel coming in
                <span x-text="countdown" class="text-gradient">M2</span>
            </h1>
            <p class="mt-4 text-base leading-relaxed text-gray-600 dark:text-gray-400">
                Halaman manajemen produk, pesanan, verifikasi pembayaran, dan resi
                akan tersedia di milestone M2 (target 2026-05-25).
            </p>
            <p class="mt-6 text-xs text-gray-500 dark:text-gray-400">
                MasFirmanPratama.com — Online Store · Sprint M1
            </p>
        </div>
    </main>
</body>
</html>
