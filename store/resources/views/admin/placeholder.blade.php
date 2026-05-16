<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — MasFirmanPratama</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 font-sans antialiased">
    <main class="flex min-h-screen items-center justify-center px-4">
        <div x-data="{ countdown: 'M2' }" class="max-w-xl rounded-3xl bg-white/80 p-10 text-center shadow-xl ring-1 ring-slate-200 backdrop-blur">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-indigo-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-indigo-700">
                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                Coming soon
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Admin panel coming in <span x-text="countdown" class="text-gradient bg-gradient-to-r from-indigo-600 to-teal-600 bg-clip-text text-transparent">M2</span>
            </h1>
            <p class="mt-4 text-base leading-relaxed text-slate-600">
                Halaman manajemen produk, pesanan, verifikasi pembayaran, dan resi
                akan tersedia di milestone M2 (target 2026-05-25).
            </p>
            <p class="mt-6 text-xs text-slate-400">
                MasFirmanPratama.com — Online Store · Sprint M1
            </p>
        </div>
    </main>
</body>
</html>
