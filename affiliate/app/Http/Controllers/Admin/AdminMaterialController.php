<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminMaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::latest()->paginate(15);

        return view('admin.materials.index', compact('materials'));
    }

    public function create(): View
    {
        return view('admin.materials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video,document,template',
            'file' => 'required|file|max:51200', // 50MB max
            'allowed_types' => 'nullable|array',
        ]);

        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        Material::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'allowed_types' => $request->allowed_types,
            'is_active' => true,
        ]);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil diupload.');
    }

    public function destroy(Material $material): RedirectResponse
    {
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function toggle(Material $material): RedirectResponse
    {
        $material->update(['is_active' => ! $material->is_active]);
        $status = $material->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Materi berhasil {$status}.");
    }
}
