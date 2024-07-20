<?php

namespace Yogaap\PHP\MVC\Config;

use Symfony\Component\Yaml\Yaml;

class Database
{
    private static ?\PDO $instance = null;

    public static function getConnection(): \PDO
    {
        if (self::$instance === null) {
            $config = self::loadConfig();

            $pdoConfig = $config['database'];

            $url = "mysql:host={$pdoConfig['host']}:{$pdoConfig['port']};dbname={$pdoConfig['db_name']}";
            $username = $pdoConfig['user'];
            $password = $pdoConfig['password'];

            self::$instance = new \PDO($url, $username, $password);
        }

        return self::$instance;
    }

    private static function loadConfig(): array
    {
        $configFile = __DIR__ . '/../../config.yml';
        return Yaml::parseFile($configFile);
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