<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use Kenjis\CI3Compatible\Library\Seeder;

class CategorySeeder extends Seeder
{
    private $table = 'category';

    public function run()
    {
        echo __METHOD__ . ": $this->table\n";
    }
}
