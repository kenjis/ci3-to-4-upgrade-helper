<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\TestSupport;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\LogTestHelperTrait;
use Kenjis\PhpUnitHelper\ReflectionHelper;
use Kenjis\PhpUnitHelper\TestDouble;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use TestDouble;
    use ReflectionHelper;
    use LogTestHelperTrait;

    public function setUp(): void
    {
        // Initialize controller
        new CI_Controller();
    }
}
