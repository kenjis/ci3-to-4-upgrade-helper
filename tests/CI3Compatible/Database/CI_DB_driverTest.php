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
        $this->markTestIncomplete();
    }

    public function test_query_update(): void
    {
        // @TODO
        $this->markTestIncomplete();
    }

    public function test_query_delete(): void
    {
        // @TODO
        $this->markTestIncomplete();
    }

    public function test_query_binding(): void
    {
        // @TODO
        $this->markTestIncomplete();
    }

    public function test_query_return_object(): void
    {
        // @TODO
        $this->markTestIncomplete();
    }

    public function test_count_all(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->driver->count_all();
    }

    public function test_trans_start_success()
    {
        $this->driver->trans_start();
        $this->driver->query(
            "INSERT INTO `db_news` (`title`, `slug`, `body`) VALUES ('title1', 'title1', 'body1')"
        );
        $this->driver->trans_complete();

        $this->assertTrue($this->driver->trans_status());
    }

    public function test_trans_begin_commit()
    {
        $this->driver->trans_begin();
        $this->driver->query(
            "INSERT INTO `db_news` (`title`, `slug`, `body`) VALUES ('title3', 'title3', 'body3')"
        );
        $this->driver->trans_commit();

        $this->seeInDatabase('db_news', [
            'slug' => 'title3',
        ]);
    }

    public function test_trans_begin_rollback()
    {
        $this->driver->trans_begin();
        $this->driver->query(
            "INSERT INTO `db_news` (`title`, `slug`, `body`) VALUES ('title4', 'title4', 'body4')"
        );
        $this->driver->trans_rollback();

        $this->dontSeeInDatabase('db_news', [
            'slug' => 'title4',
        ]);
    }
}
