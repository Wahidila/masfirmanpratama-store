<x-admin.card>
    <form method="POST" action="{{ route('admin.settings.bank-accounts.update') }}"
        x-data="{
            accounts: {{ json_encode(empty($bankAccounts) ? [] : $bankAccounts) }},
            addRow() {
                this.accounts.push({ bank: '', number: '', holder: '', logo_color: 'slate', primary: false });
            },
            removeRow(idx) {
                this.accounts.splice(idx, 1);
            },
        }">
        @csrf
        @method('PUT')

        <div class="space-y-3">
            <template x-for="(acc, idx) in accounts" :key="idx">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Bank</label>
                        <input type="text" :name="`bank_accounts[${idx}][bank]`" x-model="acc.bank" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Nomor</label>
                        <input type="text" :name="`bank_accounts[${idx}][number]`" x-model="acc.number" required
                            class="h-11 w-full font-mono rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Atas nama</label>
                        <input type="text" :name="`bank_accounts[${idx}][holder]`" x-model="acc.holder"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Warna logo</label>
                        <select :name="`bank_accounts[${idx}][logo_color]`" x-model="acc.logo_color"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="slate">Slate</option>
                            <option value="sky">Sky (BCA)</option>
                            <option value="amber">Amber (Mandiri)</option>
                            <option value="emerald">Emerald (BSI)</option>
                            <option value="red">Red (BRI)</option>
                            <option value="blue">Blue (BNI)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <label class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-700 mb-2 dark:text-gray-300">
                            <input type="checkbox" :name="`bank_accounts[${idx}][primary]`" :value="1"
                                :checked="acc.primary" @change="acc.primary = $event.target.checked"
                                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700">
                            Primary
                        </label>
                        <button type="button" @click="removeRow(idx)"
                            class="ml-auto inline-flex items-center gap-1 rounded-lg border border-error-200 bg-white px-3 py-1.5 text-xs font-medium text-error-600 hover:bg-error-50 transition dark:border-error-500/30 dark:bg-white/[0.03] dark:text-error-500 dark:hover:bg-error-500/15">
                            <x-admin.icon name="trash" class="h-3 w-3" />
                            Hapus
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="accounts.length === 0" x-cloak class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                Belum ada rekening. Klik <span class="font-medium text-gray-700 dark:text-gray-300">Tambah Rekening</span> untuk mulai.
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
            <x-admin.button type="button" variant="outline" @click="addRow()">
                <x-admin.icon name="plus" class="h-3.5 w-3.5" />
                Tambah Rekening
            </x-admin.button>

            <x-admin.button type="submit">
                Simpan semua rekening
            </x-admin.button>
        </div>
    </form>
</x-admin.card>
