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

    public function test_result_num_rows(): void
    {
        $query = $this->queryBuilder->get('news');

        $this->assertSame(3, $query->num_rows());
    }

    public function test_get_where_one_record_row_array(): void
    {
        $slug = 'caffeination-yes';
        $query = $this->queryBuilder->get_where('news', ['slug' => $slug]);
        $row = $query->row_array();

        $this->assertSame($slug, $row['slug']);
    }

    public function test_get_where_one_record_row(): void
    {
        $slug = 'caffeination-yes';
        $query = $this->queryBuilder->get_where('news', ['slug' => $slug]);
        $row = $query->row();

        $this->assertSame($slug, $row->slug);
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

    public function test_select(): void
    {
        $this->queryBuilder->select('COUNT(*) AS count');
        $query = $this->queryBuilder->get('news');
        $row = $query->row_array();

        $this->assertSame(3, $row['count']);
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

    public function test_delete(): void
    {
        $slug = 'news-title';
        $this->queryBuilder->where('slug', $slug);
        $ret = $this->queryBuilder->delete('news');

        $this->assertInstanceOf(CI_DB_result::class, $ret);
        $this->dontSeeInDatabase('news', ['slug' => $slug]);
    }
}
