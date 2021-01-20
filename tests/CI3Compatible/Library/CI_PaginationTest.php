<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use Config\Services;
use Kenjis\CI3Compatible\TestCase;

class CI_PaginationTest extends TestCase
{
    public function test_create_links(): void
    {
        Services::reset(true);

        $pagination = new CI_Pagination();
        $config['base_url'] = 'http://example.com/index.php/test/page/';
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $pagination->initialize($config);

        $html = $pagination->create_links();

        $this->assertStringContainsString(
            '<a href="http://example.com?page=10" aria-label="Last">',
            $html
        );
    }
}
