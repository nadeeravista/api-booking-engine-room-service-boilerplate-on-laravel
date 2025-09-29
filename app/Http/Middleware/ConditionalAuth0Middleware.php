<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConditionalAuth0Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('auth0.enabled', false)) {
            return app(\App\Http\Middleware\Auth0Middleware::class)->handle($request, $next);
        }

        return $next($request);
    }
}
