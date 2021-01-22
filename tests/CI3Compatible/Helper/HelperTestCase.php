<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Helper;

use Kenjis\CI3Compatible\TestCase;

abstract class HelperTestCase extends TestCase
{
    protected function loadHelper(string $name)
    {
        require __DIR__ . '/../../../src/CI3Compatible/Helper/' . $name . '_helper.php';
    }
}
