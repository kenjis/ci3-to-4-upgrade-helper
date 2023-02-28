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

    public function test_where_in_one_record(): void
    {
        $slug = 'caffeination-yes';
        $this->queryBuilder->where_in('slug', [$slug]);
        $query = $this->queryBuilder->get('news');
        $row = $query->row_array();

        $this->assertSame($slug, $row['slug']);
    }

    public function test_where_in_two_records(): void
    {
        $slugs = ['caffeination-yes', 'elvis-sighted'];
        $this->queryBuilder->where_in('slug', $slugs);
        $query = $this->queryBuilder->get('news');
        $result = $query->result_array();

        $this->assertCount(2, $result);
    }

    public function test_or_where(): void
    {
        $slug1 = 'caffeination-yes';
        $this->queryBuilder->where('slug', $slug1);
        $slug2 = 'elvis-sighted';
        $this->queryBuilder->or_where('slug', $slug2);
        $query = $this->queryBuilder->get('news');
        $result = $query->result_array();

        $this->assertCount(2, $result);
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

    public function test_select_sum(): void
    {
        $this->queryBuilder->select_sum('subtotal');
        $this->queryBuilder->from('cart');
        $this->queryBuilder->where('user_id', 100);
        $sql = $this->queryBuilder->get_compiled_select();

        $expected = 'SELECT SUM(`subtotal`) AS `subtotal`
FROM `db_cart`
WHERE `user_id` = 100';
        $this->assertSame($expected, $sql);
    }

    public function test_from(): void
    {
        $this->queryBuilder->from('news');
        $this->queryBuilder->get();

        $db = $this->queryBuilder->getBaseConnection();
        $sql = (string) $db->getLastQuery();
        $expected = 'SELECT *
FROM `db_news`';
        $this->assertSame($expected, $sql);
    }

    public function test_like(): void
    {
        $this->queryBuilder->like('body', 'of');
        $this->queryBuilder->like('body', 'as');
        $this->queryBuilder->get('news');

        $db = $this->queryBuilder->getBaseConnection();
        $sql = (string) $db->getLastQuery();
        $expected = "SELECT *
FROM `db_news`
WHERE `body` LIKE '%of%' ESCAPE '!'
AND  `body` LIKE '%as%' ESCAPE '!'";
        $this->assertSame($expected, $sql);
    }

    public function test_join_get_compiled_select(): void
    {
        $this->queryBuilder->select('*');
        $this->queryBuilder->from('blogs');
        $this->queryBuilder->join('comments', 'comments.id = blogs.id');
        $sql = $this->queryBuilder->get_compiled_select();

        $expected = 'SELECT *
FROM `db_blogs`
JOIN `db_comments` ON `db_comments`.`id` = `db_blogs`.`id`';
        $this->assertSame($expected, $sql);
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

    public function test_get_compiled_insert(): void
    {
        $data = [
            'title' => 'My title',
            'name'  => 'My name',
            'date'  => 'My date',
        ];
        $sql = $this->queryBuilder->set($data)->get_compiled_insert('mytable');

        $expected = "INSERT INTO `db_mytable` (`title`, `name`, `date`) VALUES ('My title', 'My name', 'My date')";
        $this->assertSame($expected, $sql);
    }

    // --------------------------------------------------------------------
    // UPDATE
    // --------------------------------------------------------------------

    public function test_get_compiled_update(): void
    {
        $data = [
            'title' => 'My Title',
            'name' => 'My Name',
            'date' => 'My Date',
        ];
        $this->queryBuilder->where('id', 1);
        $this->queryBuilder->set($data);
        $sql = $this->queryBuilder->get_compiled_update('mytable');

        $expected = "UPDATE `db_mytable` SET `title` = 'My Title', `name` = 'My Name', `date` = 'My Date'
WHERE `id` = 1";
        $this->assertSame($expected, $sql);
    }

    // --------------------------------------------------------------------
    // DELETE
    // --------------------------------------------------------------------

    public function test_delete(): void
    {
        $slug = 'news-title';
        $this->queryBuilder->where('slug', $slug);
        $ret = $this->queryBuilder->delete('news');

        $this->assertTrue($ret);
        $this->dontSeeInDatabase('news', ['slug' => $slug]);
    }

    public function test_get_compiled_delete()
    {
        $this->queryBuilder->where('id', 1);
        $sql = $this->queryBuilder->get_compiled_delete('mytable');

        $expected = 'DELETE FROM `db_mytable`
WHERE `id` = 1';
        $this->assertSame($expected, $sql);
    }

    // --------------------------------------------------------------------
    // TRUNCATE
    // --------------------------------------------------------------------

    public function test_truncate_from(): void
    {
        $this->queryBuilder->from('news');
        $this->queryBuilder->truncate();

        $db = $this->queryBuilder->getBaseConnection();
        $sql = (string) $db->getLastQuery();
        $expected = 'DELETE FROM `db_news`';
        $this->assertSame($expected, $sql);
    }

    public function test_truncate(): void
    {
        $this->queryBuilder->truncate('news');

        $db = $this->queryBuilder->getBaseConnection();
        $sql = (string) $db->getLastQuery();
        $expected = 'DELETE FROM `db_news`';
        $this->assertSame($expected, $sql);
    }
}
