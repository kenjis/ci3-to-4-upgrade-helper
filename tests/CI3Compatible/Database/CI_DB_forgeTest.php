<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

class CI_DB_forgeTest extends DatabaseTestCase
{
    /** @var CI_DB_forge */
    private $dbforge;

    public function setUp(): void
    {
        $this->dbforge = new CI_DB_forge();
    }

    public function test_create_table(): void
    {
        $fields = [
            'blog_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'blog_title' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
            ],
            'blog_author' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'King of Town',
            ],
            'blog_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('blog');

        $db = self::$connection;
        $tables = $db->listTables();
        $this->assertContains('db_blog', $tables);
    }

    public function test_drop_table(): void
    {
        $this->dbforge->drop_table('blog');

        $db = self::$connection;
        $tables = $db->listTables();
        $this->assertNotContains('db_blog', $tables);
    }
}
