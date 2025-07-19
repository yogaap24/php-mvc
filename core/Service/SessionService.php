<?php

namespace Core\Service;

class SessionService
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function flash(string $key, $value): void
    {
        self::set("flash_{$key}", $value);
    }

    public static function getFlash(string $key, $default = null)
    {
        $flashKey = "flash_{$key}";
        $value = self::get($flashKey, $default);
        self::remove($flashKey);
        return $value;
    }

    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
    }
}