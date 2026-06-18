<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialDownload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    public function index(Request $request): View
    {
        $affiliator = Auth::guard('affiliator')->user();

        $query = Material::where('is_active', true);

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $materials = $query->latest()->paginate(12);

        // Filter by access
        $materials->getCollection()->transform(function ($material) use ($affiliator) {
            $material->accessible = $material->isAccessibleBy($affiliator);

            return $material;
        });

        return view('materials.index', compact('materials'));
    }

    public function download(Material $material): StreamedResponse|RedirectResponse
    {
        $affiliator = Auth::guard('affiliator')->user();

        if (! $material->isAccessibleBy($affiliator)) {
            return redirect()->route('materials.index')
                ->withErrors(['access' => 'Anda tidak memiliki akses ke materi ini.']);
        }

        // Log download
        MaterialDownload::create([
            'material_id' => $material->id,
            'affiliator_id' => $affiliator->id,
            'downloaded_at' => now(),
        ]);

        $material->increment('download_count');

        return Storage::download($material->file_path);
    }
}
