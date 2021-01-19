<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

class CI_DBTest extends DatabaseTestCase
{
    use SeederNewsTable;

    /** @var CI_DB */
    private $ciDb;

    public function setUp(): void
    {
        $this->ciDb = new CI_DB(self::$connection);
    }

    public function test_insert_id(): void
    {
        $sql = 'INSERT INTO db_news (title, slug, body)'
            . " VALUES ('abc', 'abc', 'body body body')";
        $this->ciDb->query($sql);

        $id = $this->ciDb->insert_id();
        $this->assertSame(4, $id);
    }
}
