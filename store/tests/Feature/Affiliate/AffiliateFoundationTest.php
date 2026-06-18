<?php

namespace Tests\Feature\Affiliate;

use App\Models\AffiliateEvent;
use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Material;
use App\Models\Order;
use App\Models\ReferralClick;
use App\Models\ReferralCode;
use App\Models\ReferralOrder;
use App\Models\Withdrawal;
use Database\Seeders\AffiliateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateFoundationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Migration smoke ───────────────────────────────────────────────

    public function test_migrate_fresh_berhasil_tanpa_error(): void
    {
        // RefreshDatabase sudah jalankan migrate:fresh.
        // Kalau sampai sini berarti semua migration jalan.
        $this->assertTrue(true);
    }

    // ─── Factory smoke — tiap factory bisa create row ──────────────────

    public function test_factory_affiliator_membuat_row(): void
    {
        $affiliator = Affiliator::factory()->create();
        $this->assertDatabaseHas('affiliators', ['id' => $affiliator->id]);
    }

    public function test_factory_referral_code_membuat_row(): void
    {
        $code = ReferralCode::factory()->create();
        $this->assertDatabaseHas('referral_codes', ['id' => $code->id]);
    }

    public function test_factory_referral_click_membuat_row(): void
    {
        $click = ReferralClick::factory()->create();
        $this->assertDatabaseHas('referral_clicks', ['id' => $click->id]);
    }

    public function test_factory_referral_order_membuat_row(): void
    {
        $ro = ReferralOrder::factory()->create();
        $this->assertDatabaseHas('referral_orders', ['id' => $ro->id]);
    }

    public function test_factory_commission_membuat_row(): void
    {
        $commission = Commission::factory()->create();
        $this->assertDatabaseHas('commissions', ['id' => $commission->id]);
    }

    public function test_factory_commission_setting_membuat_row(): void
    {
        $setting = CommissionSetting::factory()->create();
        $this->assertDatabaseHas('commission_settings', ['id' => $setting->id]);
    }

    public function test_factory_withdrawal_membuat_row(): void
    {
        $withdrawal = Withdrawal::factory()->create();
        $this->assertDatabaseHas('withdrawals', ['id' => $withdrawal->id]);
    }

    public function test_factory_material_membuat_row(): void
    {
        $material = Material::factory()->create();
        $this->assertDatabaseHas('materials', ['id' => $material->id]);
    }

    public function test_factory_affiliate_event_membuat_row(): void
    {
        $event = AffiliateEvent::factory()->create();
        $this->assertDatabaseHas('affiliate_events', ['id' => $event->id]);
    }

    // ─── Relasi ────────────────────────────────────────────────────────

    public function test_affiliator_has_many_referral_codes(): void
    {
        $affiliator = Affiliator::factory()->create();
        ReferralCode::factory()->count(2)->create(['affiliator_id' => $affiliator->id]);

        $this->assertCount(2, $affiliator->referralCodes);
    }

    public function test_affiliator_has_many_commissions(): void
    {
        $affiliator = Affiliator::factory()->create();
        $order = Order::factory()->create();
        Commission::factory()->count(3)->create([
            'affiliator_id' => $affiliator->id,
            'order_id' => $order->id,
        ]);

        $this->assertCount(3, $affiliator->commissions);
    }

    public function test_affiliator_has_many_withdrawals(): void
    {
        $affiliator = Affiliator::factory()->create();
        Withdrawal::factory()->count(2)->create(['affiliator_id' => $affiliator->id]);

        $this->assertCount(2, $affiliator->withdrawals);
    }

    public function test_referral_code_belongs_to_affiliator(): void
    {
        $code = ReferralCode::factory()->create();

        $this->assertInstanceOf(Affiliator::class, $code->affiliator);
    }

    public function test_referral_code_has_many_clicks(): void
    {
        $code = ReferralCode::factory()->create();
        ReferralClick::factory()->count(3)->create(['referral_code_id' => $code->id]);

        $this->assertCount(3, $code->referralClicks);
    }

    public function test_referral_order_has_one_commission(): void
    {
        $affiliator = Affiliator::factory()->create();
        $order = Order::factory()->create();
        $ro = ReferralOrder::factory()->create(['order_id' => $order->id]);
        Commission::factory()->create([
            'affiliator_id' => $affiliator->id,
            'referral_order_id' => $ro->id,
            'order_id' => $order->id,
        ]);

        $this->assertInstanceOf(Commission::class, $ro->commission);
    }

    public function test_withdrawal_belongs_to_affiliator(): void
    {
        $withdrawal = Withdrawal::factory()->create();

        $this->assertInstanceOf(Affiliator::class, $withdrawal->affiliator);
    }

    // ─── Seeder ────────────────────────────────────────────────────────

    public function test_affiliate_seeder_membuat_3_affiliator(): void
    {
        $this->seed(AffiliateSeeder::class);

        $this->assertDatabaseCount('affiliators', 3);
    }

    public function test_affiliate_seeder_membuat_commission_setting_global(): void
    {
        $this->seed(AffiliateSeeder::class);

        $this->assertDatabaseHas('commission_settings', [
            'scope' => 'global',
            'rate_percent' => '10.00',
        ]);
    }

    public function test_affiliate_seeder_membuat_referral_code_per_affiliator(): void
    {
        $this->seed(AffiliateSeeder::class);

        $this->assertDatabaseCount('referral_codes', 3);
    }

    public function test_affiliate_seeder_membuat_materials(): void
    {
        $this->seed(AffiliateSeeder::class);

        $this->assertDatabaseCount('materials', 3);
    }

    public function test_affiliate_seeder_membuat_event_aktif(): void
    {
        $this->seed(AffiliateSeeder::class);

        $this->assertDatabaseHas('affiliate_events', ['status' => 'active']);
    }

    // ─── Soft delete affiliator ────────────────────────────────────────

    public function test_affiliator_soft_delete_tidak_hilang_dari_database(): void
    {
        $affiliator = Affiliator::factory()->create();
        $affiliator->delete();

        $this->assertSoftDeleted('affiliators', ['id' => $affiliator->id]);
        $this->assertDatabaseHas('affiliators', ['id' => $affiliator->id]);
    }
}
