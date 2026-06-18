<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\ReferralService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CaptureReferral — intercept ?ref=CODE pada semua route web.
 *
 * Jika request memiliki query param `ref` dan code valid (ada di referral_codes):
 *   1. Set cookie 'ref_code' (30 hari, encrypted via Laravel default)
 *   2. Increment referral_codes.clicks_count
 *   3. Log ReferralClick (ip_hash, user_agent, landing_url)
 */
class CaptureReferral
{
    public function __construct(private ReferralService $referralService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');

        if (is_string($code) && $code !== '') {
            $cookieDays = 30;
            $this->referralService->captureClick($code, $request);

            /** @var Response $response */
            $response = $next($request);

            // Hanya set cookie jika code valid (ReferralCode exists)
            if ($this->referralService->isValidCode($code)) {
                $cookie = cookie('ref_code', $code, $cookieDays * 24 * 60);
                $response->headers->setCookie($cookie);
            }

            return $response;
        }

        return $next($request);
    }
}
