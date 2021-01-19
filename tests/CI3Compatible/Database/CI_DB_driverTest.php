<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use Config\Database;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

class CI_DB_driverTest extends DatabaseTestCase
{
    /** @var CI_DB_driver */
    private $driver;

    public function setUp(): void
    {
        $this->driver = new CI_DB_driver(self::$connection);
    }

    protected static function createTable(): void
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

    protected static function seedData(): void
    {
        self::$connection->table('news')->truncate();

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

    public function test_count_all(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->driver->count_all();
    }
}
