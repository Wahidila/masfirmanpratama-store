<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMaterialRequest;
use App\Http\Requests\Admin\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(Request $request): View
    {
        $query = Material::query();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->input('search').'%');
        }

        $stats = [
            'total' => Material::count(),
            'banner' => Material::where('type', 'banner')->count(),
            'brosur' => Material::where('type', 'brosur')->count(),
            'video' => Material::where('type', 'video')->count(),
            'template_wa' => Material::where('type', 'template_wa')->count(),
        ];

        $materials = $query->latest()->paginate(20)->withQueryString();

        return view('admin.materials.index', compact('materials', 'stats'));
    }

    public function create(): View
    {
        return view('admin.materials.create');
    }

    public function store(StoreMaterialRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', 'public');
            $data['file_path'] = 'storage/'.$path;
        }

        unset($data['file']);
        Material::create($data);

        return redirect()
            ->route('admin.materials.index')
            ->with('status', 'Materi berhasil ditambahkan.');
    }

    public function edit(Material $material): View
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(UpdateMaterialRequest $request, Material $material): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($material->file_path) {
                $oldPath = str_replace('storage/', '', $material->file_path);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('file')->store('materials', 'public');
            $data['file_path'] = 'storage/'.$path;
        }

        unset($data['file']);
        $material->update($data);

        return redirect()
            ->route('admin.materials.index')
            ->with('status', 'Materi berhasil diperbarui.');
    }

    public function destroy(Material $material): RedirectResponse
    {
        if ($material->file_path) {
            $oldPath = str_replace('storage/', '', $material->file_path);
            Storage::disk('public')->delete($oldPath);
        }

        $material->delete();

        return redirect()
            ->route('admin.materials.index')
            ->with('status', 'Materi berhasil dihapus.');
    }
}
