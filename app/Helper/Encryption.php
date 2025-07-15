<?php

namespace Yogaap\PHP\MVC\Helper;

use Yogaap\PHP\MVC\Config\Environment;

class Encryption
{
    private const CIPHER_METHOD = 'AES-256-CBC';

    public static function encrypt(string $data): string
    {
        $key = Environment::get('ENCRYPTION_KEY');

        if (empty($key)) {
            throw new \Exception('ENCRYPTION_KEY is not set in environment variables');
        }

        if (strlen($key) !== 32) {
            throw new \Exception('ENCRYPTION_KEY must be exactly 32 characters long');
        }

        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, self::CIPHER_METHOD, $key, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(string $encryptedData): string
    {
        $key = Environment::get('ENCRYPTION_KEY');

        if (empty($key)) {
            throw new \Exception('ENCRYPTION_KEY is not set in environment variables');
        }

        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt($encrypted, self::CIPHER_METHOD, $key, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception('Failed to decrypt data');
        }

        return $decrypted;
    }

    public static function generateKey(): string
    {
        return bin2hex(random_bytes(16)); // 32 characters
    }
}
