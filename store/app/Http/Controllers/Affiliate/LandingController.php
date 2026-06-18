<?php

declare(strict_types=1);

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing program affiliate.
     */
    public function index(): View
    {
        return view('affiliate.landing');
    }
}
