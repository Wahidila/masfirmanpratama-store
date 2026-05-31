<x-admin.card>
    <form method="POST" action="{{ route('admin.settings.store-info.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Nama toko" name="name" required>
                <input type="text" id="name" name="name" value="{{ old('name', $storeInfo['name']) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </x-admin.form-group>

            <x-admin.form-group label="Tagline" name="tagline">
                <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $storeInfo['tagline']) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </x-admin.form-group>
        </div>

        <x-admin.form-group label="Alamat" name="address" hint="Dipakai di footer + halaman kontak.">
            <textarea id="address" name="address" rows="2"
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('address', $storeInfo['address']) }}</textarea>
        </x-admin.form-group>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Kota" name="city">
                <input type="text" id="city" name="city" value="{{ old('city', $storeInfo['city']) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </x-admin.form-group>

            <x-admin.form-group label="Jam operasional" name="operating_hours">
                <input type="text" id="operating_hours" name="operating_hours" value="{{ old('operating_hours', $storeInfo['operating_hours']) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </x-admin.form-group>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Telepon" name="phone">
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $storeInfo['phone']) }}"
                    placeholder="62812..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
            </x-admin.form-group>

            <x-admin.form-group label="Email" name="email">
                <input type="email" id="email" name="email" value="{{ old('email', $storeInfo['email']) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </x-admin.form-group>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
            <x-admin.button type="submit">
                Simpan perubahan
            </x-admin.button>
        </div>
    </form>
</x-admin.card>
