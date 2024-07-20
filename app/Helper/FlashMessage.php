<?php

namespace Yogaap\PHP\MVC\Helper;

class FlashMessage
{
    private const FLASH_KEY = 'flash_messages';
    private const COOKIE_EXPIRY = 5; // Cookie expires in 5 seconds

    public static function addMessage(string $type, string $message): void
    {
        $flashMessages = self::getFlashMessages();
        $flashMessages[$type] = $message; // Hanya menyimpan satu pesan per tipe

        // Set cookie dengan pesan flash
        setcookie(self::FLASH_KEY, json_encode($flashMessages), time() + self::COOKIE_EXPIRY, "/");
    }

    public static function getMessages(): array
    {
        if (!isset($_COOKIE[self::FLASH_KEY])) {
            return [];
        }

        $flashMessages = json_decode($_COOKIE[self::FLASH_KEY], true) ?? [];
        // Clear messages after retrieval
        self::clearMessages();

        return $flashMessages;
    }

    private static function clearMessages(): void
    {
        setcookie(self::FLASH_KEY, '', time() - 3600, "/"); // Hapus cookie
    }

    private static function getFlashMessages(): array
    {
        return isset($_COOKIE[self::FLASH_KEY]) ? json_decode($_COOKIE[self::FLASH_KEY], true) : [];
    }
}