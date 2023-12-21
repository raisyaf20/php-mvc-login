<?php

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;

class DatabaseTest extends  TestCase
{
    public function testConnection()
    {
        $conn = Database::getConnection();
        self::assertNotNull($conn);
    }

    public function testConnectionSingleton()
    {
        $conn1 = Database::getConnection();
        $conn2 = Database::getConnection();
        self::assertSame($conn1, $conn2);
    }
}
