<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use App\Database\Seeds\CategorySeeder;
use Config\Database;
use Kenjis\CI3Compatible\TestCase;

class SeederTest extends TestCase
{
    public function test_call_(): void
    {
        $dbConfig = new Database();
        $seeder = new CategorySeeder($dbConfig);
        $seeder->setPath_(__DIR__ . '/../../App/Database/Seeds');

        $this->expectOutputString(
            'App\Database\Seeds\CategorySeeder::run: category
App\Database\Seeds\FavoriteSeeder::run: favorite
'
        );

        $seeder->call_('FavoriteSeeder');
    }
}
