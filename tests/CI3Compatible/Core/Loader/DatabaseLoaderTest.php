<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\TestCase;

class DatabaseLoaderTest extends TestCase
{
    /** @var News */
    private $controller;

    /** @var DatabaseLoader */
    private $loader;

    public function setUp(): void
    {
        $this->controller = new News();
        $injector = new ControllerPropertyInjector($this->controller);
        $this->loader = new DatabaseLoader($injector);
    }

    public function test_load_database(): void
    {
        $this->loader->load();

        $this->assertInstanceOf(
            CI_DB::class,
            $this->controller->db
        );
    }

    public function test_load_database_twice(): void
    {
        $this->loader->load();
        $db1 = $this->controller->db;

        $this->loader->load();
        $db2 = $this->controller->db;

        $this->assertInstanceOf(
            CI_DB::class,
            $this->controller->db
        );
        $this->assertSame($db1, $db2);
    }

    public function test_load_database_two_instances(): void
    {
        $db1 = $this->loader->load('', true);
        $db2 = $this->loader->load('', true);

        $this->assertNotSame($db1, $db2);
        $this->assertInstanceOf(CI_DB::class, $db1);
        $this->assertInstanceOf(CI_DB::class, $db2);
    }
}
