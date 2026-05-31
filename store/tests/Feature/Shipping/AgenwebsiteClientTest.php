<?php

namespace Tests\Feature\Shipping;

use App\Services\Shipping\AgenwebsiteClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AgenwebsiteClientTest extends TestCase
{
    public function test_sends_wordpress_user_agent_and_form_body_to_license_endpoint(): void
    {
        Http::fake([
            '*/license/activate' => Http::response([
                'data' => ['type' => 'exclusive', 'shipping_quota' => 'Unlimited'],
                'message' => 'Berhasil terkoneksi dengan Agenwebsite',
            ], 200),
        ]);

        $client = app(AgenwebsiteClient::class);
        $result = $client->activateLicense();

        $this->assertSame('success', $result['status']);
        Http::assertSent(function ($request) {
            return str_contains($request->header('User-Agent')[0], 'WordPress/')
                && $request['product'] === 'agenwebsite-shipping'
                && $request->hasHeader('site-url');
        });
    }
}
