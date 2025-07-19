<?php

namespace Core\Security;

class Encryption
{
    private string $key;
    private string $cipher;

    public function __construct(string $key = null)
    {
        $this->key = $key ?? $_ENV['APP_KEY'] ?? 'default-key-change-in-production';
        $this->cipher = 'AES-256-CBC';
    }

    public function encrypt(string $data): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);

        return base64_encode($encrypted . '::' . $iv);
    }

    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        $parts = explode('::', $data, 2);

        if (count($parts) !== 2) {
            throw new \Exception('Invalid encrypted data format');
        }

        list($encrypted, $iv) = $parts;

        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception('Failed to decrypt data');
        }

        return $decrypted;
    }

    public function hash(string $data): string
    {
        return hash('sha256', $data . $this->key);
    }

    public function generateKey(): string
    {
        return base64_encode(random_bytes(32));
    }
}