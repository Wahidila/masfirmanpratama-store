<?php

namespace Tests\Feature\Affiliate;

use App\Models\ReferralClick;
use App\Models\ReferralCode;
use Database\Seeders\AffiliateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiting_with_valid_ref_code_sets_cookie_and_creates_click(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $initialClicks = $referralCode->clicks_count;

        $response = $this->get('/affiliate?ref='.$referralCode->code);

        $response->assertStatus(200);
        $response->assertCookie('ref_code', $referralCode->code);

        // Click logged
        $this->assertDatabaseHas('referral_clicks', [
            'referral_code_id' => $referralCode->id,
        ]);

        // clicks_count incremented
        $referralCode->refresh();
        $this->assertEquals($initialClicks + 1, $referralCode->clicks_count);

        // ReferralClick has correct data
        $click = ReferralClick::where('referral_code_id', $referralCode->id)->first();
        $this->assertNotNull($click);
        $this->assertNotEmpty($click->ip_hash);
        $this->assertNotEmpty($click->landing_url);
        $this->assertStringContainsString('ref='.$referralCode->code, $click->landing_url);
    }

    public function test_visiting_with_invalid_ref_code_does_not_set_cookie(): void
    {
        $this->seed(AffiliateSeeder::class);

        $response = $this->get('/affiliate?ref=INVALIDCODE999');

        $response->assertStatus(200);
        $response->assertCookieMissing('ref_code');

        // No click logged for invalid code
        $this->assertDatabaseMissing('referral_clicks', [
            'landing_url' => request()->fullUrl(),
        ]);
        $this->assertEquals(0, ReferralClick::count());
    }

    public function test_visiting_without_ref_param_does_not_set_cookie(): void
    {
        $this->seed(AffiliateSeeder::class);

        $response = $this->get('/affiliate');

        $response->assertStatus(200);
        $response->assertCookieMissing('ref_code');
        $this->assertEquals(0, ReferralClick::count());
    }

    public function test_ref_code_works_on_any_route_not_just_affiliate(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $response = $this->get('/?ref='.$referralCode->code);

        $response->assertStatus(200);
        $response->assertCookie('ref_code', $referralCode->code);

        $this->assertDatabaseHas('referral_clicks', [
            'referral_code_id' => $referralCode->id,
        ]);
    }

    public function test_ip_hash_is_sha256_not_plain_ip(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $this->get('/affiliate?ref='.$referralCode->code);

        $click = ReferralClick::first();
        $this->assertNotNull($click);
        // SHA-256 hash is 64 hex chars
        $this->assertEquals(64, strlen($click->ip_hash));
    }
}
