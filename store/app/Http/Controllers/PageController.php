<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function tentang(): View
    {
        return view('pages.tentang');
    }

    public function kontak(): View
    {
        return view('pages.kontak');
    }
}
