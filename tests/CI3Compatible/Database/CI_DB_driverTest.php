<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use Kenjis\CI3Compatible\Exception\NotSupportedException;

class CI_DB_driverTest extends DatabaseTestCase
{
    use SeederNewsTable;

    /** @var CI_DB_driver */
    private $driver;

    public function setUp(): void
    {
        $this->driver = new CI_DB_driver(self::$connection);
    }

    public function test_count_all(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->driver->count_all();
    }
}
