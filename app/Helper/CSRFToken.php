<?php

namespace Yogaap\PHP\MVC\Helper;

use Yogaap\PHP\MVC\Config\Environment;

class CSRFToken
{
    private static ?string $token = null;

    public static function generate(): string
    {
        if (self::$token === null) {
            self::startSession();

            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            self::$token = $_SESSION['csrf_token'];
        }

        return self::$token;
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        self::startSession();

        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function getTokenName(): string
    {
        return Environment::get('CSRF_TOKEN_NAME', '_token');
    }

    public static function getHiddenInput(): string
    {
        $tokenName = self::getTokenName();
        $tokenValue = self::generate();

        return "<input type=\"hidden\" name=\"{$tokenName}\" value=\"{$tokenValue}\">";
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function regenerate(): void
    {
        self::startSession();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        self::$token = $_SESSION['csrf_token'];
    }
}
