<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Kenjis\CI3Compatible\DatabaseTestHelperTrait;
use Kenjis\CI3Compatible\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    use DatabaseTestHelperTrait;

    /** @var BaseConnection */
    protected static $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::connectDb();
        static::createTable();
        static::seedData();
    }

    protected static function connectDb(): void
    {
        self::$connection = Database::connect();
    }

    protected static function createTable(): void
    {
    }

    protected static function seedData(): void
    {
    }
}
