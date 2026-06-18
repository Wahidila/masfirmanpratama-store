<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliatorIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('affiliator')->user();

        if ($user && $user->email_verified_at === null) {
            return redirect()->route('affiliate.verification.notice');
        }

        return $next($request);
    }
}
