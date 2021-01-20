<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

class CI_DB_query_builderTest extends DatabaseTestCase
{
    use SeederNewsTable;

    /** @var CI_DB_query_builder */
    private $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = new CI_DB_query_builder(self::$connection);
    }

    // --------------------------------------------------------------------
    // SELECT
    // --------------------------------------------------------------------

    public function test_get_all_records(): void
    {
        $query = $this->queryBuilder->get('news');
        $result = $query->result_array();

        $this->assertCount(3, $result);
    }

    public function test_get_where_one_record(): void
    {
        $slug = 'caffeination-yes';
        $query = $this->queryBuilder->get_where('news', ['slug' => $slug]);
        $row = $query->row_array();

        $this->assertSame($slug, $row['slug']);
    }

    public function test_where_one_record(): void
    {
        $slug = 'caffeination-yes';
        $this->queryBuilder->where('slug', $slug);
        $query = $this->queryBuilder->get('news');
        $row = $query->row_array();

        $this->assertSame($slug, $row['slug']);
    }

    public function test_order_by(): void
    {
        $this->queryBuilder->order_by('title', 'ASC');
        $this->queryBuilder->get('news');

        $db = $this->queryBuilder->getBaseConnection();
        $sql = (string) $db->getLastQuery();
        $expected = 'SELECT *
FROM `db_news`
ORDER BY `title` ASC';
        $this->assertSame($expected, $sql);
    }

    public function test_count_all(): void
    {
        $count = $this->queryBuilder->count_all('news');

        $this->assertSame(3, $count);
    }

    // --------------------------------------------------------------------
    // INSERT
    // --------------------------------------------------------------------

    public function test_insert_one_record(): void
    {
        $data = [
            'title' => 'News Title',
            'slug'  => 'news-title',
            'body'  => 'News body',
        ];

        $ret = $this->queryBuilder->insert('news', $data);

        $this->assertTrue($ret);
        $this->seeInDatabase('news', ['slug' => 'news-title']);
    }

    // --------------------------------------------------------------------
    // DELETE
    // --------------------------------------------------------------------

}
