<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Service\SessionService;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!$this->isAuthenticated()) {
            return new Response(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    private function isAuthenticated(): bool
    {
        return SessionService::has('user_id');
    }

    public static function check(): bool
    {
        return SessionService::has('user_id');
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return SessionService::get('user_data');
    }

    public static function login(array $userData): void
    {
        SessionService::set('user_id', $userData['id']);
        SessionService::set('user_data', $userData);
        SessionService::regenerate();
    }

    public static function logout(): void
    {
        SessionService::remove('user_id');
        SessionService::remove('user_data');
        SessionService::regenerate();
    }
}