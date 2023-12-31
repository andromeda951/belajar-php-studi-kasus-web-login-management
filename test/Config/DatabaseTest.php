<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{

    public function testgetConnecton()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
    }

    public function testgetConnectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }

}

