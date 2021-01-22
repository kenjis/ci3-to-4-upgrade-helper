<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Helper;

class url_helperTest extends HelperTestCase
{
    public function test_base_url__and_string(): void
    {
        $this->loadHelper('url');

        $url = base_url_() . 'captcha/';

        $this->assertSame('http://example.com/captcha/', $url);
    }

    public function test_base_url__with_param(): void
    {
        $url = base_url_('captcha/');

        $this->assertSame('http://example.com/captcha/', $url);
    }
}
