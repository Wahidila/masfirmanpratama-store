<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Services\ReferralService;

/**
 * CreditAffiliateCommission — listener untuk PaymentVerified.
 *
 * Trigger: admin approve bukti bayar → OrderController::approvePayment.
 * Action: credit affiliate commission jika order punya referral attribution.
 */
class CreditAffiliateCommission
{
    public function __construct(private ReferralService $referralService) {}

    public function handle(PaymentVerified $event): void
    {
        $this->referralService->creditForOrder($event->order);
    }
}
