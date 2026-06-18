<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\ReferralClick;
use App\Models\ReferralCode;
use App\Models\ReferralOrder;
use Illuminate\Http\Request;

/**
 * ReferralService — handles referral click capture, order attribution, and commission crediting.
 */
class ReferralService
{
    /**
     * Check if a referral code exists and is valid.
     */
    public function isValidCode(string $code): bool
    {
        return ReferralCode::where('code', $code)->exists();
    }

    /**
     * Capture a referral click: increment clicks_count + log ReferralClick.
     * Idempotent-ish: hanya proses jika code valid.
     */
    public function captureClick(string $code, Request $request): void
    {
        $referralCode = ReferralCode::where('code', $code)->first();

        if ($referralCode === null) {
            return;
        }

        // Increment clicks count
        $referralCode->increment('clicks_count');

        // Log click
        ReferralClick::create([
            'referral_code_id' => $referralCode->id,
            'ip_hash' => hash('sha256', (string) $request->ip()),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            'landing_url' => mb_substr($request->fullUrl(), 0, 2000),
        ]);
    }

    /**
     * Attach order to referral code via referral_orders pivot.
     * Called at order creation time. Reads ref_code from cookie.
     * Skip if no valid code or self-referral (order email = affiliator email).
     */
    public function attachOrder(Order $order, ?string $code): void
    {
        if ($code === null || $code === '') {
            return;
        }

        $referralCode = ReferralCode::where('code', $code)->first();

        if ($referralCode === null) {
            return;
        }

        // Guard: self-referral (affiliator's email == order email)
        $affiliator = $referralCode->affiliator;
        if ($affiliator !== null && $order->email !== null && $affiliator->email === $order->email) {
            return;
        }

        // Guard: don't duplicate — already linked
        if (ReferralOrder::where('referral_code_id', $referralCode->id)->where('order_id', $order->id)->exists()) {
            return;
        }

        ReferralOrder::create([
            'referral_code_id' => $referralCode->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Credit commission for an order — IDEMPOTENT.
     *
     * Called on PaymentVerified. Only credits when order status is fully 'paid'.
     * Guards:
     *   - Only process referral_orders with status=pending
     *   - Skip if Commission already exists for this order
     *   - Resolve rate: type-scoped CommissionSetting override > global fallback
     *   - Amount = round(order total * rate / 100)
     */
    public function creditForOrder(Order $order): void
    {
        // Only credit when fully paid
        if ($order->status !== 'paid') {
            return;
        }

        // Guard: don't double-credit — check if commission already exists for this order
        if (Commission::where('order_id', $order->id)->exists()) {
            return;
        }

        // Find pending referral_order for this order
        $referralOrder = ReferralOrder::where('order_id', $order->id)
            ->where('status', 'pending')
            ->first();

        if ($referralOrder === null) {
            return;
        }

        $referralCode = $referralOrder->referralCode;
        if ($referralCode === null) {
            return;
        }

        $affiliator = $referralCode->affiliator;
        if ($affiliator === null) {
            return;
        }

        // Resolve commission rate: type-scoped override else global
        $rate = $this->resolveRate($affiliator->type);

        if ($rate <= 0) {
            return;
        }

        // Compute commission amount from order total (excluding shipping)
        $subtotal = $this->computeOrderSubtotal($order);
        $amount = (int) round($subtotal * $rate / 100);

        if ($amount <= 0) {
            return;
        }

        // Create commission
        Commission::create([
            'affiliator_id' => $affiliator->id,
            'referral_order_id' => $referralOrder->id,
            'order_id' => $order->id,
            'amount' => $amount,
            'rate' => $rate,
            'status' => 'pending',
        ]);

        // Flip referral_order status to credited
        $referralOrder->update(['status' => 'credited']);
    }

    /**
     * Resolve commission rate: check type-specific setting first, fallback to global.
     */
    protected function resolveRate(?string $affiliatorType): float
    {
        // Try type-scoped override: scope = 'type:<affiliator_type>'
        if ($affiliatorType !== null) {
            $typeScope = "type:{$affiliatorType}";
            $typeSetting = CommissionSetting::where('scope', $typeScope)->first();
            if ($typeSetting !== null) {
                return (float) $typeSetting->rate_percent;
            }
        }

        // Fallback to global
        $globalSetting = CommissionSetting::where('scope', 'global')->first();

        return $globalSetting !== null ? (float) $globalSetting->rate_percent : 0.0;
    }

    /**
     * Compute order subtotal (total minus shipping cost) for commission basis.
     */
    protected function computeOrderSubtotal(Order $order): int
    {
        $total = (int) $order->total;
        $shippingCost = (int) ($order->shipping_cost ?? 0);

        return max(0, $total - $shippingCost);
    }
}
