<x-admin.card>
    <form method="POST" action="{{ route('admin.settings.shipping.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Kota asal pengiriman" name="origin" required
                hint="Nama kota tempat pengiriman berasal.">
                <input type="text" id="origin" name="origin" value="{{ old('origin', $shippingData['origin']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>

            <x-admin.form-group label="Kode pos asal" name="origin_zipcode" required>
                <input type="text" id="origin_zipcode" name="origin_zipcode" value="{{ old('origin_zipcode', $shippingData['origin_zipcode']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-group label="Berat default (kg)" name="default_weight_kg" required
                hint="Berat default untuk produk tanpa berat, minimal 0.1 kg.">
                <input type="number" id="default_weight_kg" name="default_weight_kg" step="0.1" min="0.1" max="100"
                    value="{{ old('default_weight_kg', $shippingData['default_weight_kg']) }}"
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </x-admin.form-group>

            <x-admin.form-group label="Aktifkan ongkos kirim">
                <div class="flex items-center gap-3 pt-1">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                        <input type="hidden" name="shipping_enabled" value="0">
                        <input type="checkbox" name="shipping_enabled" value="1" role="switch"
                            @checked(old('shipping_enabled', $shippingData['shipping_enabled']))
                            class="h-5 w-9 rounded-full border-slate-300 bg-slate-200 checked:bg-primary-600 focus:ring-primary-500 focus:ring-offset-0 transition-colors cursor-pointer">
                        <span>{{ $shippingData['shipping_enabled'] ? 'Aktif' : 'Nonaktif' }}</span>
                    </label>
                </div>
            </x-admin.form-group>
        </div>

        <x-admin.form-group label="Kurir aktif" hint="Centang kurir yang ingin ditampilkan saat checkout.">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1">
                @php $activeCouriers = old('couriers', $shippingData['couriers'] ?? []); @endphp
                @foreach ($availableCouriers as $courier)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="couriers[]" value="{{ $courier }}"
                            @checked(in_array($courier, $activeCouriers))
                            class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        {{ strtoupper($courier) }}
                    </label>
                @endforeach
            </div>
        </x-admin.form-group>

        <x-admin.form-group label="Markup biaya per layanan" name="service_markup"
            hint="Satu baris per layanan dengan format: service_id:markup. Contoh: jne_reg:5000. Kosongkan jika tanpa markup.">
            <textarea id="service_markup" name="service_markup" rows="4"
                class="block w-full rounded-xl border-slate-200 text-sm font-mono focus:border-primary-500 focus:ring-primary-500/40"
                placeholder="jne_reg:5000&#10;jnt_reg:3000&#10;sicepat_reg:2000">{{ old('service_markup', $shippingData['service_markup_raw']) }}</textarea>
        </x-admin.form-group>

        <hr class="border-slate-100">

        <div>
            <h4 class="text-sm font-semibold text-slate-900 mb-3">Status Lisensi Agenwebsite</h4>

            @php $lic = $shippingData['license_status']; @endphp
            @if ($lic && ($lic['status'] ?? '') === 'success')
                <div class="flex items-start gap-3 rounded-xl border border-secondary-200 bg-secondary-50 px-4 py-3 text-sm text-secondary-900">
                    <x-admin.icon name="check" class="h-4 w-4 mt-0.5 shrink-0 text-secondary-600" />
                    <div>
                        <p class="font-medium">Terhubung</p>
                        @if (!empty($lic['result']['expire_date']))
                            <p class="mt-0.5 text-xs text-secondary-700">Lisensi berlaku hingga {{ $lic['result']['expire_date'] }}</p>
                        @endif
                    </div>
                </div>
            @elseif ($lic && ($lic['status'] ?? '') === 'error')
                <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                    <x-admin.icon name="alert-triangle" class="h-4 w-4 mt-0.5 shrink-0 text-rose-600" />
                    <div>
                        <p class="font-medium">Lisensi tidak terhubung</p>
                        @if (!empty($lic['message']))
                            <p class="mt-0.5 text-xs text-rose-700">{{ $lic['message'] }}</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <x-admin.icon name="info" class="h-4 w-4 mt-0.5 shrink-0" />
                    <div>
                        <p class="font-medium">Memeriksa lisensi...</p>
                    </div>
                </div>
            @endif

            <p class="mt-2 text-xs text-slate-500">
                Lisensi dan domain dikonfigurasi via <code class="text-slate-700 bg-slate-100 px-1 rounded">.env</code>
                (<code>AGENWEBSITE_SHIPPING_LICENSE</code>, <code>AGENWEBSITE_SHIPPING_SITE_URL</code>) —
                tidak dapat diubah dari panel ini.
            </p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2 text-sm font-medium text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition">
                Simpan pengaturan pengiriman
            </button>
        </div>
    </form>
</x-admin.card>
