<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliatorIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $affiliator = Auth::guard('affiliator')->user();

        if ($affiliator && !$affiliator->isActive()) {
            if ($affiliator->isPending()) {
                return redirect()->route('pending-approval');
            }

            Auth::guard('affiliator')->logout();
            $request->session()->invalidate();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah disuspend. Hubungi admin.']);
        }

        return $next($request);
    }
}
