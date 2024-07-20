<?php

namespace Yogaap\PHP\MVC\Tests\Config;

use PHPUnit\Framework\TestCase;
use Yogaap\PHP\MVC\Config\Database;

class DatabaseTest extends TestCase
{
    public function testDatabaseConnection()
    {
        $connection = Database::getConnection();
        self::assertInstanceOf(\PDO::class, $connection);
        self::assertNotNull($connection);
    }

    public function testDatabaseConnectionIsSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}
