<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\URI;
use Config\Services;
use Kenjis\CI3Compatible\TestCase;

class CI_PaginationTest extends TestCase
{
    public function test_create_links(): void
    {
        Services::reset(true);

        $uri = new URI('http://example.com/test/page/1');
        $request = Services::request();
        $request->uri = $uri;

        $pagination = new CI_Pagination();
        $config['base_url'] = 'http://example.com/test/page/';
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $pagination->initialize($config);

        $html = $pagination->create_links();

        $this->assertStringContainsString(
            '<a href="http://example.com/test/page/10" aria-label="Last">',
            $html
        );
    }
}
