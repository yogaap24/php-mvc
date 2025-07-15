<?php

namespace Yogaap\PHP\MVC\Config;

class Environment
{
    private static ?array $variables = null;

    public static function load(): void
    {
        if (self::$variables !== null) {
            return;
        }

        $envFile = $path ?? __DIR__ . '/../../.env';

        if (!file_exists($envFile)) {
            throw new \Exception('.env file not found');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        self::$variables = [];

        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            if (($value[0] === '"' && $value[-1] === '"') ||
                ($value[0] === "'" && $value[-1] === "'")) {
                $value = substr($value, 1, -1);
            }

            self::$variables[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    public static function get(string $key, $default = null)
    {
        if (self::$variables === null) {
            self::load();
        }

        return self::$variables[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        if (self::$variables === null) {
            self::load();
        }

        self::$variables[$key] = $value;
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }

    public static function has(string $key): bool
    {
        if (self::$variables === null) {
            self::load();
        }

        return isset(self::$variables[$key]);
    }
}
