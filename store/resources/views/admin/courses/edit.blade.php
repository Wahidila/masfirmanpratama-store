@extends('layouts.admin', ['active' => 'courses'])

@section('title', 'Edit Kelas · ' . $course->title)

@section('content')
    <x-admin.page-header
        title="Edit Kelas"
        :subtitle="'Edit data kelas: ' . $course->title">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.courses.index') }}" variant="outline" size="sm">
                ← Kembali ke daftar
            </x-admin.button>
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                onsubmit="return confirm('Hapus kelas &quot;{{ $course->title }}&quot;? Bisa di-restore dari arsip.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-error-200 bg-white px-4 py-2 text-xs font-medium text-error-600 hover:bg-error-50 transition dark:border-error-500/30 dark:bg-white/[0.03] dark:text-error-500 dark:hover:bg-error-500/15">
                    <x-admin.icon name="trash" class="h-3.5 w-3.5" />
                    Hapus
                </button>
            </form>
        </x-slot>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-6">
            <x-admin.alert tone="error" title="Form belum valid">
                Periksa kembali field di bawah — ada kolom yang perlu diperbaiki.
            </x-admin.alert>
        </div>
    @endif

    @include('admin.courses._form', ['course' => $course, 'mode' => 'edit'])
@endsection
