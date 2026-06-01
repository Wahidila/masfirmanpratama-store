@extends('layouts.admin', ['active' => 'courses'])

@section('title', 'Tambah Kelas')

@section('content')
    <x-admin.page-header
        title="Tambah Kelas Baru"
        subtitle="Bikin kelas baru untuk katalog store. Status default-nya draft — switch ke active kalau siap tayang.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.courses.index') }}" variant="outline" size="sm">
                ← Kembali ke daftar
            </x-admin.button>
        </x-slot>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-6">
            <x-admin.alert tone="error" title="Form belum valid">
                Periksa kembali field di bawah — ada kolom yang perlu diperbaiki.
            </x-admin.alert>
        </div>
    @endif

    @include('admin.courses._form', ['course' => $course, 'mode' => 'create'])
@endsection
