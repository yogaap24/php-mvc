<?php

namespace Yogaap\PHP\MVC\Helper;

class FlashMessage
{
    private const FLASH_KEY = 'flash_messages';

    public static function addMessage(string $type, string $message): void
    {
        self::startSession();

        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }

        $_SESSION[self::FLASH_KEY][$type] = $message;
    }

    public static function getMessages(): array
    {
        self::startSession();

        if (!isset($_SESSION[self::FLASH_KEY])) {
            return [];
        }

        $messages = $_SESSION[self::FLASH_KEY];
        unset($_SESSION[self::FLASH_KEY]);

        return $messages;
    }

    public static function hasMessages(): bool
    {
        self::startSession();
        return isset($_SESSION[self::FLASH_KEY]) && !empty($_SESSION[self::FLASH_KEY]);
    }

    public static function getMessage(string $type): ?string
    {
        self::startSession();

        if (!isset($_SESSION[self::FLASH_KEY][$type])) {
            return null;
        }

        $message = $_SESSION[self::FLASH_KEY][$type];
        unset($_SESSION[self::FLASH_KEY][$type]);

        return $message;
    }

    public static function clearMessages(): void
    {
        self::startSession();
        unset($_SESSION[self::FLASH_KEY]);
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}