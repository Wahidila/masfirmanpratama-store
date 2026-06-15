<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * XSenderService — WhatsApp gateway via XSender API.
 *
 * Kirim pesan WA via POST ke endpoint XSender.
 * Config priority: DB settings (admin dashboard) > .env fallback.
 *
 * Format nomor: 628xxxxxxxxxx (tanpa +).
 */
class XSenderService
{
    /**
     * Send WhatsApp message via XSender API.
     *
     * @param  string  $phone  Nomor tujuan (akan di-normalize ke 628xxx)
     * @param  string  $message  Isi pesan
     * @return array{ok: bool, status: int, body: string}
     */
    public function send(string $phone, string $message): array
    {
        $apiKey = $this->getApiKey();
        $sender = $this->getSender();
        $endpoint = $this->getEndpoint();

        if (empty($apiKey) || empty($sender)) {
            Log::warning('[XSender] API Key atau Sender belum dikonfigurasi.');

            return [
                'ok' => false,
                'status' => 0,
                'body' => 'XSender not configured: missing api_key or sender.',
            ];
        }

        $phone = self::normalizePhone($phone);

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post($endpoint, [
                    'api_key' => $apiKey,
                    'sender' => $sender,
                    'number' => $phone,
                    'message' => $message,
                ]);

            $result = [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'body' => $response->body(),
            ];

            if (! $result['ok']) {
                Log::warning('[XSender] Send failed', [
                    'phone' => $phone,
                    'status' => $result['status'],
                    'body' => mb_substr($result['body'], 0, 200),
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('[XSender] Exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'status' => 0,
                'body' => 'Exception: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Normalize phone ke format 628xxxxxxxxxx.
     */
    public static function normalizePhone(string $phone): string
    {
        // Strip spasi, dash, karakter non-digit kecuali +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // +62 → 62
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        // 08xxx → 628xxx
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        // Kalau belum awali 62, tambahkan
        if (! str_starts_with($phone, '62')) {
            $phone = '62'.$phone;
        }

        return $phone;
    }

    /**
     * Get API Key: DB setting first, fallback .env.
     */
    protected function getApiKey(): ?string
    {
        return Setting::getValue('xsender.api_key')
            ?: config('services.xsender.api_key');
    }

    /**
     * Get Sender number: DB setting first, fallback .env.
     */
    protected function getSender(): ?string
    {
        $sender = Setting::getValue('xsender.sender')
            ?: config('services.xsender.sender');

        return $sender ? self::normalizePhone($sender) : null;
    }

    /**
     * Get endpoint URL: DB setting first, fallback .env/config.
     */
    protected function getEndpoint(): string
    {
        return Setting::getValue('xsender.endpoint')
            ?: config('services.xsender.endpoint', 'https://xsender.id/id/send-message');
    }
}
