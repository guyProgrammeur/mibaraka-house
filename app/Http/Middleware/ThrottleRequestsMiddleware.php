<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class ThrottleRequestsMiddleware
{
    protected $limiter;
    
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }
    
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        // Grâce à trustProxies, request->ip() récupérera la vraie IP du client à Kinshasa ou ailleurs
        $key = $this->resolveRequestSignature($request);
        
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);
            
            // Si la requête attend du JSON (API), on renvoie du JSON
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trop de requêtes. Veuillez réessayer dans ' . $retryAfter . ' secondes.'
                ], 429);
            }

            // Pour le Web et Livewire, on lève l'exception standard de Laravel (génère une vue 429 propre)
            throw new ThrottleRequestsException(
                'Trop de requêtes. Veuillez réessayer plus tard.',
                null,
                ['Retry-After' => $retryAfter]
            );
        }
        
        $this->limiter->hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        return $this->addHeaders(
            $response, 
            $maxAttempts,
            $this->limiter->remaining($key, $maxAttempts),
            $this->limiter->availableIn($key)
        );
    }
    
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->method() . '|' . $request->url() . '|' . $request->ip()
        );
    }
    
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter): Response
    {
        // S'assurer que la réponse possède une méthode d'en-tête accessible
        if (property_exists($response, 'headers')) {
            $response->headers->add([
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => $remainingAttempts,
            ]);
            
            if ($retryAfter > 0) {
                $response->headers->add([
                    'Retry-After' => $retryAfter,
                ]);
            }
        }
        
        return $response;
    }
}