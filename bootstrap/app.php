<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // CRUCIAL : Dit à Laravel de faire confiance à Nginx Proxy Manager 
        // pour sécuriser les adresses IP et forcer les liens en HTTPS
        $middleware->trustProxies(at: '*');

        // Déclaration de tes alias pour les routes
        $middleware->alias([
            'admin'    => \App\Http\Middleware\AdminMiddleware::class,
            'throttle' => \App\Http\Middleware\ThrottleRequestsMiddleware::class, // Ton limiteur personnalisé
        ]);

        // Application globale des en-têtes de sécurité sur TOUTES les requêtes web
        $middleware->append([
            \App\Http\Middleware\SecurityHeadersMiddleware::class, // Chemin complet pour éviter le crash de namespace
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();