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
        $middleware->trustProxies(at: '*');

        // [QUAN TRỌNG] Đăng ký tên tắt middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'email.verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        // Track visitors cho web
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackVisitors::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckBanned::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();