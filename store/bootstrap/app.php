<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Untuk guard 'admin', redirect unauthenticated ke /admin/login
        // (default Laravel pakai route('login') yang tidak terdefinisi di app ini).
        $middleware->redirectGuestsTo(function ($request) {
            // Hanya admin routes yang dilindungi auth:admin saat ini.
            // Web guard tidak dipakai — kalau dipakai later, tambahin guard-aware logic di sini.
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }

            return route('admin.login');
        });

        // Authenticated admin yang nyentuh guest-only routes (login page) → dashboard
        $middleware->redirectUsersTo(function ($request) {
            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
