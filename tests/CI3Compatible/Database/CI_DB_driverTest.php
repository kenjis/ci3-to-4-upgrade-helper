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

    public function test_query_select_result(): void
    {
        $ids = [];
        $query = $this->driver->query(
            'SELECT id, title, slug FROM db_news ORDER BY id'
        );
        foreach ($query->result() as $row) {
            $ids[] = $row->id;
        }

        $this->assertSame([1, 2, 3], $ids);
    }

    public function test_query_select_result_array(): void
    {
        $ids = [];
        $query = $this->driver->query(
            'SELECT id, title, slug FROM db_news ORDER BY id'
        );
        foreach ($query->result_array() as $row) {
            $ids[] = $row['id'];
        }

        $this->assertSame([1, 2, 3], $ids);
    }

    public function test_query_insert(): void
    {
        // @TODO
    }

    public function test_query_update(): void
    {
        // @TODO
    }

    public function test_query_delete(): void
    {
        // @TODO
    }

    public function test_query_binding(): void
    {
        // @TODO
    }

    public function test_query_return_object(): void
    {
        // @TODO
    }

    public function test_count_all(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->driver->count_all();
    }
}
