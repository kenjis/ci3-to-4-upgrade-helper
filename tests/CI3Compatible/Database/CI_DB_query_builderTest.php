<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Kenjis\CI3Compatible\DatabaseTestHelperTrait;
use Kenjis\CI3Compatible\TestCase;

class CI_DB_query_builderTest extends TestCase
{
    use DatabaseTestHelperTrait;

    /** @var BaseConnection */
    private static $connection;

    /** @var CI_DB_query_builder */
    private $queryBuilder;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::connectDb();
        self::createTable();
        self::seedData();
    }

    public function setUp(): void
    {
        $this->queryBuilder = new CI_DB_query_builder(self::$connection);
    }

    private static function connectDb(): void
    {
        self::$connection = Database::connect();
    }

    private static function createTable(): void
    {
        $forge = Database::forge();
        $forge->addField(
            [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => '128',
                    'null' => false,
                ],
                'slug' => [
                    'type' => 'VARCHAR',
                    'constraint' => '128',
                    'null' => false,
                ],
                'body' => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
            ]
        );
        $forge->addPrimaryKey('id');
        $forge->createTable('news', true);
    }

    private static function seedData(): void
    {
        $data = [
            [
                'title' => 'Elvis sighted',
                'slug' => 'elvis-sighted',
                'body' => 'Elvis was sighted at the Podunk internet cafe. It looked like he was writing a CodeIgniter app.',
            ],
            [
                'title' => 'Say it isn\'t so!',
                'slug' => 'say-it-isnt-so',
                'body' => 'Scientists conclude that some programmers have a sense of humor.',
            ],
            [
                'title' => 'Caffeination, Yes!',
                'slug' => 'caffeination-yes',
                'body' => 'World\'s largest coffee shop open onsite nested coffee shop for staff only.',
            ],
        ];
        self::$connection->table('news')->insertBatch($data);
    }

    public function test_get_all_records(): void
    {
        $query = $this->queryBuilder->get('news');
        $result = $query->result_array();

        $this->assertCount(3, $result);
    }

    public function test_get_one_record(): void
    {
        $slug = 'caffeination-yes';
        $query = $this->queryBuilder->get_where('news', ['slug' => $slug]);
        $row = $query->row_array();

        $this->assertSame($slug, $row['slug']);
    }

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
}
