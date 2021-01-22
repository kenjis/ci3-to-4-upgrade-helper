<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Helper;

use Kenjis\CI3Compatible\TestCase;

class url_helperTest extends TestCase
{
    public function test_base_url__and_string(): void
    {
        require __DIR__ . '/../../../src/CI3Compatible/Helper/url_helper.php';

        $url = base_url_() . 'captcha/';

        $this->assertSame('http://example.com/captcha/', $url);
    }

    public function test_base_url__with_param(): void
    {
        $url = base_url_('captcha/');

        $this->assertSame('http://example.com/captcha/', $url);
    }
}
