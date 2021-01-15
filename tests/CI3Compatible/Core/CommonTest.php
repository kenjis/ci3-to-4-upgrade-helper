<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use CodeIgniter\Exceptions\PageNotFoundException;
use Kenjis\CI3Compatible\TestCase;

class CommonTest extends TestCase
{
    public function test_show_404_throws_PageNotFoundException(): void
    {
        require __DIR__ . '/../../../src/CI3Compatible/Core/Common.php';

        $this->expectException(PageNotFoundException::class);

        show_404('Not Found');
    }
}
