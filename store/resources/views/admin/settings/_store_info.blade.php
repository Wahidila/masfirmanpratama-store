<x-admin.card>
    <form method="POST" action="{{ route('admin.settings.store-info.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Nama toko" name="name" required>
                <input type="text" id="name" name="name" value="{{ old('name', $storeInfo['name']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>

            <x-admin.form-group label="Tagline" name="tagline">
                <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $storeInfo['tagline']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>
        </div>

        <x-admin.form-group label="Alamat" name="address" hint="Dipakai di footer + halaman kontak.">
            <textarea id="address" name="address" rows="2"
                class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">{{ old('address', $storeInfo['address']) }}</textarea>
        </x-admin.form-group>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Kota" name="city">
                <input type="text" id="city" name="city" value="{{ old('city', $storeInfo['city']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>

            <x-admin.form-group label="Jam operasional" name="operating_hours">
                <input type="text" id="operating_hours" name="operating_hours" value="{{ old('operating_hours', $storeInfo['operating_hours']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Telepon" name="phone">
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $storeInfo['phone']) }}"
                    placeholder="62812..."
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>

            <x-admin.form-group label="Email" name="email">
                <input type="email" id="email" name="email" value="{{ old('email', $storeInfo['email']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2 text-sm font-medium text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition">
                Simpan perubahan
            </button>
        </div>
    </form>
</x-admin.card>
