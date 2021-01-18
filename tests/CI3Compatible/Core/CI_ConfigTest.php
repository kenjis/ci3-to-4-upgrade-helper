<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Kenjis\CI3Compatible\TestCase;

class CI_ConfigTest extends TestCase
{
    public function test_site_url(): void
    {
        $config = new CI_Config();

        $this->assertSame(
            'http://example.com/index.php',
            $config->site_url()
        );
    }
}
