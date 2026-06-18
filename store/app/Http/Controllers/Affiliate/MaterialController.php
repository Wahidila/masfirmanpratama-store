<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::latest()->get();

        return view('affiliate.dashboard.materials', [
            'materials' => $materials,
        ]);
    }
}
