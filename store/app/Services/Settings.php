<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Settings facade-like service. Read-through cache (5 menit) supaya halaman
 * publik (checkout, upload, track) ngga hit DB tiap request.
 *
 * Pakai:
 *   Settings::get('bank_accounts', config('store.bank_accounts', []))
 *   Settings::set('bank_accounts', $array, 'array')
 *   Settings::getStoreInfo()  // shortcut bundling
 *
 * Cache di-flush otomatis tiap kali set() dipanggil. Test environment
 * (SQLite in-memory) bypass cache untuk konsistensi.
 */
class Settings
{
    public const CACHE_TTL = 300; // 5 menit

    public const CACHE_PREFIX = 'settings:';

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            if (app()->environment('testing')) {
                return Setting::getValue($key, $default);
            }

            return Cache::remember(
                self::CACHE_PREFIX.$key,
                self::CACHE_TTL,
                fn () => Setting::getValue($key, $default)
            );
        } catch (\Throwable) {
            // DB belum ter-migrate (test feature lama tanpa RefreshDatabase, atau
            // first-time install) — return default biar render pages tetap jalan.
            return $default;
        }
    }

    public static function set(string $key, mixed $value, ?string $type = null): Setting
    {
        $row = Setting::setValue($key, $value, $type);

        Cache::forget(self::CACHE_PREFIX.$key);

        return $row;
    }

    public static function forget(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX.$key);
    }

    /**
     * Bundle setting "store info" dipakai di footer / contact / receipts.
     *
     * @return array<string, mixed>
     */
    public static function getStoreInfo(): array
    {
        return [
            'name' => self::get('store.name', config('app.name', 'MasFirmanPratama')),
            'tagline' => self::get('store.tagline', 'Mind Power & Life Mastery'),
            'address' => self::get('store.address', ''),
            'city' => self::get('store.city', 'Jakarta'),
            'phone' => self::get('store.phone', ''),
            'email' => self::get('store.email', ''),
            'operating_hours' => self::get('store.operating_hours', 'Senin-Jumat 09:00-17:00 WIB'),
        ];
    }

    /**
     * Bank accounts list. Fallback ke config('store.bank_accounts') untuk
     * backward compat selama M2 transition (sampai admin isi semua).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getBankAccounts(): array
    {
        $accounts = self::get('bank_accounts');

        if (is_array($accounts) && count($accounts) > 0) {
            return $accounts;
        }

        // Fallback config (M1 dummy)
        return config('store.bank_accounts', []);
    }

    /**
     * WhatsApp admin contact.
     *
     * @return array{number: string, label: string}
     */
    public static function getWaAdmin(): array
    {
        $wa = self::get('wa_admin');

        if (is_array($wa) && isset($wa['number'])) {
            return $wa;
        }

        return config('store.wa_admin', [
            'number' => '6281234567890',
            'label' => 'Admin',
        ]);
    }
}
