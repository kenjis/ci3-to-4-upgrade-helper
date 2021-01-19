<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use Config\Database;

trait SeederNewsTable
{
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

    /**
     * Reset autoincrement sequence number in SQLite3
     *
     * @param string $table
     */
    protected static function resetSQLite3AutoIncrement(string $table): void
    {
        $db = self::$connection;

        if ($db->DBDriver === 'SQLite3') {
            $db->query(
                "DELETE FROM sqlite_sequence WHERE name='" . $db->DBPrefix . $table . "';"
            );
        }
    }

    protected static function seedData(): void
    {
        $db = self::$connection;
        $db->table('news')->truncate();
        self::resetSQLite3AutoIncrement('news');

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
        $db->table('news')->insertBatch($data);
    }
}
