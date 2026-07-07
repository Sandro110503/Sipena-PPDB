<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.admin'   => \App\Http\Middleware\RedirectIfNotAdmin::class,
            'guest.admin'  => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'auth.siswa'   => \App\Http\Middleware\RedirectIfNotSiswa::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            // Jika request ke prefix /admin, redirect ke admin login
            if ($request->is('admin/*') || $request->is('admin')) {
                return redirect()->route('admin.login');
            }
        });
    })->create();
