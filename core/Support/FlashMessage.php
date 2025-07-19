<?php

namespace Core\Support;

use Core\Service\SessionService;

class FlashMessage
{
    public static function set(string $key, string $message, string $type = 'info'): void
    {
        SessionService::set("flash_{$key}", [
            'message' => $message,
            'type' => $type
        ]);
    }

    public static function get(string $key): ?array
    {
        $flashKey = "flash_{$key}";
        $flash = SessionService::get($flashKey);

        if ($flash) {
            SessionService::remove($flashKey);
        }

        return $flash;
    }

    public static function success(string $message): void
    {
        self::set('message', $message, 'success');
    }

    public static function error(string $message): void
    {
        self::set('message', $message, 'error');
    }

    public static function warning(string $message): void
    {
        self::set('message', $message, 'warning');
    }

    public static function info(string $message): void
    {
        self::set('message', $message, 'info');
    }

    public static function getMessage(): ?array
    {
        return self::get('message');
    }

    public static function has(string $key): bool
    {
        return SessionService::has("flash_{$key}");
    }

    public static function hasMessage(): bool
    {
        return self::has('message');
    }
}