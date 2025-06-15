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
        // Adiciona nossos middlewares customizados ao final do grupo 'web'
        $middleware->web(append: [
            \App\Http\Middleware\ModoManutencaoMiddleware::class,
            \App\Http\Middleware\UpdateUserLastSeenAt::class, // <-- AJUSTE: Adicionamos o novo middleware aqui
        ]);
    })
    ->withProviders([
        // Registra nosso provedor de eventos para os Observers funcionarem
        App\Providers\EventServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();