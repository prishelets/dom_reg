<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',   // ← добавляем api.php
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // alias для нашего токен-мидлваря
        $middleware->alias([
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
        ]);

        // НИЧЕГО больше не трогаем:
        // web и api группы остаются дефолтные:
        // - web с CSRF
        // - api БЕЗ CSRF
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
