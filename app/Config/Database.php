<?php

namespace App\Config;

use App\Config\Environment;

class Database
{
    private static ?\PDO $instance = null;

    public static function getConnection(): \PDO
    {
        if (self::$instance === null) {
            Environment::load();

            $driver = Environment::get('DB_DRIVER', 'mysql');
            $host = Environment::get('DB_HOST', '127.0.0.1');
            $port = Environment::get('DB_PORT', '3306');
            $database = Environment::get('DB_DATABASE', 'php_mvc');
            $username = Environment::get('DB_USERNAME', 'root');
            $password = Environment::get('DB_PASSWORD', '');

            $dsn = self::buildDsn($driver, $host, $port, $database);
            $options = self::getConnectionOptions($driver);

            self::$instance = new \PDO($dsn, $username, $password, $options);
        }

        return self::$instance;
    }

    private static function buildDsn(string $driver, string $host, string $port, string $database): string
    {
        switch ($driver) {
            case 'pgsql':
            case 'postgres':
                return "pgsql:host={$host};port={$port};dbname={$database};options='--client_encoding=UTF8'";

            case 'sqlite':
                return "sqlite:{$database}";

            case 'mysql':
            default:
                return "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        }
    }

    private static function getConnectionOptions(string $driver): array
    {
        $baseOptions = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        switch ($driver) {
            case 'mysql':
                $baseOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
                break;

            case 'pgsql':
            case 'postgres':
                // PostgreSQL specific options if needed
                break;
        }

        return $baseOptions;
    }

    public static function beginTransaction(){
        self::$instance->beginTransaction();
    }

    public static function commitTransaction(){
        self::$instance->commit();
    }

    public static function rollbackTransaction(){
        self::$instance->rollBack();
    }
}