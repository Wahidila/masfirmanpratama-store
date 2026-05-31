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
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-white/[0.03] p-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bank</label>
                        <input type="text" :name="`bank_accounts[${idx}][bank]`" x-model="acc.bank" required
                            class="block w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nomor</label>
                        <input type="text" :name="`bank_accounts[${idx}][number]`" x-model="acc.number" required
                            class="block w-full font-mono rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Atas nama</label>
                        <input type="text" :name="`bank_accounts[${idx}][holder]`" x-model="acc.holder"
                            class="block w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Warna logo</label>
                        <select :name="`bank_accounts[${idx}][logo_color]`" x-model="acc.logo_color"
                            class="block w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500">
                            <option value="slate">Slate</option>
                            <option value="sky">Sky (BCA)</option>
                            <option value="amber">Amber (Mandiri)</option>
                            <option value="emerald">Emerald (BSI)</option>
                            <option value="red">Red (BRI)</option>
                            <option value="blue">Blue (BNI)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <label class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <input type="checkbox" :name="`bank_accounts[${idx}][primary]`" :value="1"
                                :checked="acc.primary" @change="acc.primary = $event.target.checked"
                                class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                            Primary
                        </label>
                        <button type="button" @click="removeRow(idx)"
                            class="ml-auto inline-flex items-center gap-1 rounded-full border border-rose-200 bg-white px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                            <x-admin.icon name="trash" class="h-3 w-3" />
                            Hapus
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="accounts.length === 0" x-cloak class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada rekening. Klik <span class="font-medium text-gray-700 dark:text-gray-300">Tambah Rekening</span> untuk mulai.
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
            <button type="button" @click="addRow()"
                class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">
                <x-admin.icon name="plus" class="h-3.5 w-3.5" />
                Tambah Rekening
            </button>

            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2 text-sm font-medium text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition">
                Simpan semua rekening
            </button>
        </div>
    </form>
</x-admin.card>
