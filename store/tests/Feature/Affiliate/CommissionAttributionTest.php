<?php

namespace Tests\Feature\Affiliate;

use App\Events\PaymentVerified;
use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\ReferralCode;
use App\Models\ReferralOrder;
use App\Services\ReferralService;
use Database\Seeders\AffiliateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionAttributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_attach_order_creates_referral_order_pending(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create(['status' => 'pending', 'email' => 'customer@test.com']);

        $service = app(ReferralService::class);
        $service->attachOrder($order, $referralCode->code);

        $this->assertDatabaseHas('referral_orders', [
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_attach_order_skips_invalid_code(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $service = app(ReferralService::class);
        $service->attachOrder($order, 'NONEXISTENT');

        $this->assertEquals(0, ReferralOrder::count());
    }

    public function test_attach_order_skips_null_code(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $service = app(ReferralService::class);
        $service->attachOrder($order, null);

        $this->assertEquals(0, ReferralOrder::count());
    }

    public function test_attach_order_prevents_self_referral(): void
    {
        $this->seed(AffiliateSeeder::class);

        $affiliator = Affiliator::first();
        $this->assertNotNull($affiliator);

        $referralCode = ReferralCode::where('affiliator_id', $affiliator->id)->first();
        $this->assertNotNull($referralCode);

        // Order email matches affiliator email = self-referral
        $order = Order::factory()->create([
            'status' => 'pending',
            'email' => $affiliator->email,
        ]);

        $service = app(ReferralService::class);
        $service->attachOrder($order, $referralCode->code);

        $this->assertEquals(0, ReferralOrder::count());
    }

    public function test_attach_order_does_not_duplicate(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create(['status' => 'pending', 'email' => 'customer@test.com']);

        $service = app(ReferralService::class);
        $service->attachOrder($order, $referralCode->code);
        $service->attachOrder($order, $referralCode->code); // duplicate

        $this->assertEquals(1, ReferralOrder::count());
    }

    public function test_credit_for_order_creates_commission_on_paid_order(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create([
            'status' => 'paid',
            'total' => 500000,
            'shipping_cost' => 0,
            'email' => 'customer@test.com',
        ]);

        // Create referral_order manually (simulating attachOrder was called at checkout)
        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $service = app(ReferralService::class);
        $service->creditForOrder($order);

        // Commission created
        $this->assertEquals(1, Commission::count());
        $commission = Commission::first();
        $this->assertNotNull($commission);
        $this->assertEquals($referralCode->affiliator_id, $commission->affiliator_id);
        $this->assertEquals($order->id, $commission->order_id);
        $this->assertEquals('pending', $commission->status);

        // Rate = 10% (global from seeder), amount = 500000 * 10 / 100 = 50000
        $globalSetting = CommissionSetting::where('scope', 'global')->first();
        $this->assertNotNull($globalSetting);
        $expectedRate = (float) $globalSetting->rate_percent;
        $expectedAmount = (int) round(500000 * $expectedRate / 100);
        $this->assertEquals($expectedAmount, (int) $commission->amount);
        $this->assertEquals($expectedRate, (float) $commission->rate);

        // referral_order flipped to credited
        $referralOrder = ReferralOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($referralOrder);
        $this->assertEquals('credited', $referralOrder->status);
    }

    public function test_credit_for_order_is_idempotent(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create([
            'status' => 'paid',
            'total' => 300000,
            'shipping_cost' => 0,
            'email' => 'customer@test.com',
        ]);

        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $service = app(ReferralService::class);
        $service->creditForOrder($order);
        $service->creditForOrder($order); // second call — must NOT double-create

        $this->assertEquals(1, Commission::count());
    }

    public function test_credit_for_order_does_not_credit_partial_paid(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create([
            'status' => 'partial_paid',
            'total' => 400000,
            'shipping_cost' => 0,
            'email' => 'customer@test.com',
        ]);

        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $service = app(ReferralService::class);
        $service->creditForOrder($order);

        // No commission for partial_paid
        $this->assertEquals(0, Commission::count());
        // referral_order stays pending
        $referralOrder = ReferralOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($referralOrder);
        $this->assertEquals('pending', $referralOrder->status);
    }

    public function test_payment_verified_event_triggers_commission_credit(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create([
            'status' => 'paid',
            'total' => 200000,
            'shipping_cost' => 0,
            'email' => 'buyer@test.com',
        ]);

        $payment = OrderPayment::factory()->create([
            'order_id' => $order->id,
            'amount' => 200000,
            'status' => 'verified',
        ]);

        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        // Fire the event (same as admin approving payment)
        PaymentVerified::dispatch($order, $payment);

        // Commission should be created by the listener
        $this->assertEquals(1, Commission::count());
        $commission = Commission::first();
        $this->assertNotNull($commission);
        $this->assertEquals($order->id, $commission->order_id);
    }

    public function test_credit_excludes_shipping_cost_from_commission_base(): void
    {
        $this->seed(AffiliateSeeder::class);

        $referralCode = ReferralCode::first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create([
            'status' => 'paid',
            'total' => 520000, // 500000 product + 20000 shipping
            'shipping_cost' => 20000,
            'email' => 'customer@test.com',
        ]);

        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $service = app(ReferralService::class);
        $service->creditForOrder($order);

        $commission = Commission::first();
        $this->assertNotNull($commission);
        // 10% of (520000 - 20000) = 10% of 500000 = 50000
        $this->assertEquals(50000, (int) $commission->amount);
    }

    public function test_type_scoped_commission_rate_overrides_global(): void
    {
        $this->seed(AffiliateSeeder::class);

        // Create type-specific override for alumni: 15%
        CommissionSetting::create([
            'scope' => 'type:alumni',
            'rate_percent' => 15.00,
            'min_payout' => 50000.00,
        ]);

        $affiliator = Affiliator::where('type', 'alumni')->first();
        $this->assertNotNull($affiliator);

        $referralCode = ReferralCode::where('affiliator_id', $affiliator->id)->first();
        $this->assertNotNull($referralCode);

        $order = Order::factory()->create([
            'status' => 'paid',
            'total' => 1000000,
            'shipping_cost' => 0,
            'email' => 'vip@test.com',
        ]);

        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $service = app(ReferralService::class);
        $service->creditForOrder($order);

        $commission = Commission::first();
        $this->assertNotNull($commission);
        // 15% of 1000000 = 150000
        $this->assertEquals(150000, (int) $commission->amount);
        $this->assertEquals(15.00, (float) $commission->rate);
    }
}
