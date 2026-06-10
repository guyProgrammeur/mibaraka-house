<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeadersMiddleware
{
    /**
     * En-têtes de sécurité HTTP
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // En-têtes de sécurité essentiels
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy (CSP) - Version corrigée pour Alpine.js
        $response->headers->set('Content-Security-Policy', $this->getCspPolicy());
        
        // HSTS (HTTPS uniquement - s'active en production)
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
    
    /**
     * Politique CSP corrigée pour Alpine.js
     */
    private function getCspPolicy(): string
    {
        $policies = [
            "default-src 'self'",
            
            // Alpine.js a besoin de 'unsafe-eval' pour son fonctionnement
            // Ajout également de 'wasm-unsafe-eval' pour les futures fonctionnalités
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com https://unpkg.com https://static.cloudflareinsights.com",
            
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: https://*.cloudinary.com https://via.placeholder.com https://placehold.co https://quickchart.io https://*.softyik.com",
            "connect-src 'self' https://static.cloudflareinsights.com https://*.softyik.com",
            "frame-src 'self' https://www.google.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        
        return implode('; ', $policies);
    }
}