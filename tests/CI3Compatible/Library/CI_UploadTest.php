<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use Kenjis\CI3Compatible\TestCase;

class CI_UploadTest extends TestCase
{
    public function test_(): void
    {
        $config = [];
        $upload = new CI_Upload($config);

        $this->assertInstanceOf(CI_Upload::class, $upload);
    }
}
