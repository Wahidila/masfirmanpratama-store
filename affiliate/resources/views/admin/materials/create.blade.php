@extends('admin.layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.materials.index') }}" class="text-sm text-primary-600">&larr; Kembali</a>
    <h1 class="text-xl font-bold text-slate-800 mt-2">Upload Materi Baru</h1>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                <input type="text" name="title" required value="{{ old('title') }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 outline-none resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipe</label>
                <select name="type" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="image">Gambar</option>
                    <option value="video">Video</option>
                    <option value="document">Dokumen</option>
                    <option value="template">Template</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">File (max 50MB)</label>
                <input type="file" name="file" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700 file:font-medium hover:file:bg-primary-100">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700">Upload</button>
        </form>
    </div>
</div>
@endsection
