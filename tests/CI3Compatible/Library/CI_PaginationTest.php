<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\URI;
use Config\Services;
use Kenjis\CI3Compatible\TestSupport\TestCase;

class CI_PaginationTest extends TestCase
{
    public function test_create_links(): void
    {
        Services::reset(true);

        $configApp = config('App');
        $configApp->indexPage = '';

        $_SERVER['REQUEST_URI'] = '/test/page/1';
        $uri = new URI('http://example.com/test/page/1');
        $request = Services::request();
        $request->uri = $uri;

        $pagination = new CI_Pagination();
        // 'base_url' does not work, because CI4 Pager uses `current_url()`
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
