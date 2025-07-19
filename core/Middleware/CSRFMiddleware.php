<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Security\CSRF;

class CSRFMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        // Skip CSRF validation for GET requests
        if ($request->getMethod() === 'GET') {
            return $next($request);
        }

        // Check CSRF token
        $token = $request->getPost('_token') ?? $request->getServer('HTTP_X_CSRF_TOKEN');

        if (!$token || !CSRF::validateToken($token)) {
            return new Response(['error' => 'CSRF token mismatch'], 419);
        }

        return $next($request);
    }
}