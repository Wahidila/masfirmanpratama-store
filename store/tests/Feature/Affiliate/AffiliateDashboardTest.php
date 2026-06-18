<?php

namespace Tests\Feature\Affiliate;

use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Material;
use App\Models\Order;
use App\Models\ReferralCode;
use App\Models\ReferralOrder;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test dashboard affiliator — auth gate, ringkasan, referral, komisi privacy,
 * withdraw validation, event gate alumni+peserta only.
 */
class AffiliateDashboardTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ───────────────────────────────────────────────────────

    private function verifiedAffiliator(string $type = 'alumni'): Affiliator
    {
        return Affiliator::factory()->{$type === 'non_alumni' ? 'nonAlumni' : ($type === 'peserta' ? 'peserta' : 'alumni')}()->create();
    }

    private function unverifiedAffiliator(): Affiliator
    {
        return Affiliator::factory()->pending()->create();
    }

    // ─── Auth + Verified Gate ─────────────────────────────────────────

    public function test_dashboard_requires_auth(): void
    {
        $this->get(route('affiliate.dashboard'))
            ->assertRedirect(route('affiliate.login'));
    }

    public function test_dashboard_requires_verified_email(): void
    {
        $affiliator = $this->unverifiedAffiliator();

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.dashboard'))
            ->assertRedirect(route('affiliate.verification.notice'));
    }

    public function test_dashboard_renders_for_verified_affiliator(): void
    {
        $affiliator = $this->verifiedAffiliator();

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.dashboard'))
            ->assertOk()
            ->assertViewIs('affiliate.dashboard.index');
    }

    // ─── Dashboard Ringkasan ──────────────────────────────────────────

    public function test_dashboard_shows_summary_data(): void
    {
        $affiliator = $this->verifiedAffiliator();
        ReferralCode::factory()->create([
            'affiliator_id' => $affiliator->id,
            'clicks_count' => 42,
        ]);
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'amount' => 100000,
            'status' => 'approved',
        ]);

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.dashboard'))
            ->assertOk()
            ->assertSee('42')         // total klik
            ->assertSee('100.000');   // komisi
    }

    // ─── Referral Link ────────────────────────────────────────────────

    public function test_generate_referral_link_creates_code(): void
    {
        $affiliator = $this->verifiedAffiliator();

        $this->actingAs($affiliator, 'affiliator')
            ->post(route('affiliate.referral-links.store'))
            ->assertRedirect(route('affiliate.referral-links.index'));

        $this->assertDatabaseHas('referral_codes', [
            'affiliator_id' => $affiliator->id,
        ]);
    }

    public function test_referral_links_page_lists_codes(): void
    {
        $affiliator = $this->verifiedAffiliator();
        $code = ReferralCode::factory()->create([
            'affiliator_id' => $affiliator->id,
            'code' => 'TESTCODE',
        ]);

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.referral-links.index'))
            ->assertOk()
            ->assertSee('TESTCODE');
    }

    // ─── Komisi — Privacy (NAMA saja, tanpa email/phone) ─────────────

    public function test_commission_list_shows_only_customer_name(): void
    {
        $affiliator = $this->verifiedAffiliator();
        $order = Order::factory()->create([
            'customer_name' => 'Budi Santoso',
            'email' => 'budi@secret.com',
            'phone' => '081234567890',
        ]);
        $referralCode = ReferralCode::factory()->create(['affiliator_id' => $affiliator->id]);
        $referralOrder = ReferralOrder::factory()->create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
        ]);
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'referral_order_id' => $referralOrder->id,
            'order_id' => $order->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.commissions.index'));

        $response->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('budi@secret.com')
            ->assertDontSee('081234567890');
    }

    // ─── Withdrawal Validation ────────────────────────────────────────

    public function test_withdraw_requires_minimum_payout(): void
    {
        $affiliator = $this->verifiedAffiliator();
        CommissionSetting::factory()->create([
            'scope' => 'global',
            'min_payout' => 50000,
        ]);
        // Beri saldo approved
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'amount' => 200000,
            'status' => 'approved',
        ]);

        $this->actingAs($affiliator, 'affiliator')
            ->post(route('affiliate.withdrawals.store'), ['amount' => 10000])
            ->assertSessionHasErrors('amount');
    }

    public function test_withdraw_cannot_exceed_available_balance(): void
    {
        $affiliator = $this->verifiedAffiliator();
        CommissionSetting::factory()->create([
            'scope' => 'global',
            'min_payout' => 50000,
        ]);
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'amount' => 100000,
            'status' => 'approved',
        ]);

        $this->actingAs($affiliator, 'affiliator')
            ->post(route('affiliate.withdrawals.store'), ['amount' => 500000])
            ->assertSessionHasErrors('amount');
    }

    public function test_withdraw_requires_bank_info(): void
    {
        $affiliator = Affiliator::factory()->alumni()->create([
            'bank_name' => null,
            'bank_account' => null,
            'bank_holder' => null,
        ]);
        CommissionSetting::factory()->create([
            'scope' => 'global',
            'min_payout' => 50000,
        ]);
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'amount' => 200000,
            'status' => 'approved',
        ]);

        $this->actingAs($affiliator, 'affiliator')
            ->post(route('affiliate.withdrawals.store'), ['amount' => 100000])
            ->assertRedirect(route('affiliate.profile.edit'))
            ->assertSessionHas('error');
    }

    public function test_withdraw_creates_row_with_status_requested(): void
    {
        $affiliator = $this->verifiedAffiliator();
        CommissionSetting::factory()->create([
            'scope' => 'global',
            'min_payout' => 50000,
        ]);
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'amount' => 200000,
            'status' => 'approved',
        ]);

        $this->actingAs($affiliator, 'affiliator')
            ->post(route('affiliate.withdrawals.store'), ['amount' => 100000])
            ->assertRedirect(route('affiliate.withdrawals.index'));

        $this->assertDatabaseHas('withdrawals', [
            'affiliator_id' => $affiliator->id,
            'amount' => '100000.00',
            'status' => 'requested',
        ]);
    }

    // ─── Materi ───────────────────────────────────────────────────────

    public function test_materials_page_lists_materials(): void
    {
        $affiliator = $this->verifiedAffiliator();
        Material::factory()->create(['title' => 'Banner Promo Kelas']);

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.materials.index'))
            ->assertOk()
            ->assertSee('Banner Promo Kelas');
    }

    // ─── Event/Leaderboard — Gate alumni+peserta only ─────────────────

    public function test_event_page_accessible_for_alumni(): void
    {
        $affiliator = $this->verifiedAffiliator('alumni');

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.events.index'))
            ->assertOk();
    }

    public function test_event_page_accessible_for_peserta(): void
    {
        $affiliator = $this->verifiedAffiliator('peserta');

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.events.index'))
            ->assertOk();
    }

    public function test_event_page_forbidden_for_non_alumni(): void
    {
        $affiliator = $this->verifiedAffiliator('non_alumni');

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.events.index'))
            ->assertForbidden();
    }

    // ─── Profile ──────────────────────────────────────────────────────

    public function test_profile_edit_renders(): void
    {
        $affiliator = $this->verifiedAffiliator();

        $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.profile.edit'))
            ->assertOk()
            ->assertViewIs('affiliate.dashboard.profile');
    }

    public function test_profile_update_saves_bank_info(): void
    {
        $affiliator = $this->verifiedAffiliator();

        $this->actingAs($affiliator, 'affiliator')
            ->put(route('affiliate.profile.update'), [
                'phone' => '081999888777',
                'bank_name' => 'BCA',
                'bank_account' => '1234567890',
                'bank_holder' => 'Budi Test',
            ])
            ->assertRedirect(route('affiliate.profile.edit'));

        $this->assertDatabaseHas('affiliators', [
            'id' => $affiliator->id,
            'bank_name' => 'BCA',
            'bank_account' => '1234567890',
            'bank_holder' => 'Budi Test',
        ]);
    }
}
