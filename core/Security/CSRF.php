<?php

namespace Core\Security;

use Core\Security\SessionService;

class CSRF
{
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        SessionService::set('csrf_token', $token);
        return $token;
    }

    public static function getToken(): ?string
    {
        return SessionService::get('csrf_token');
    }

    public static function validateToken(string $token): bool
    {
        $sessionToken = SessionService::get('csrf_token');

        if (!$sessionToken) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function verifyToken(string $token): void
    {
        if (!self::validateToken($token)) {
            throw new \Exception('CSRF token mismatch', 419);
        }
    }

    public static function field(): string
    {
        $token = self::getToken() ?? self::generateToken();
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }

    public static function meta(): string
    {
        $token = self::getToken() ?? self::generateToken();
        return '<meta name="csrf-token" content="' . $token . '">';
    }
}