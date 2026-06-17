@extends('layouts.app')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-slate-900 px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-xl font-bold text-white">Admin Panel</h1>
            <p class="text-slate-400 text-sm mt-1">Affiliate MasFirmanPratama</p>
        </div>
        <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700">
            @if($errors->any())
                <div class="mb-4 p-3 bg-rose-900/30 border border-rose-700 rounded-xl text-rose-300 text-sm">
                    @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-slate-300 mb-1">Email</label>
                    <input type="email" name="email" required autofocus class="w-full px-4 py-2.5 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-500 transition">Masuk</button>
            </form>
        </div>
    </div>
</div>
@endsection
